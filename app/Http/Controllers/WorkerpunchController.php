<?php

namespace App\Http\Controllers;

use App\Models\Punch;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkerPunchController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SR status values — CHANGE THESE to match your service_requests.status ENUM
    |--------------------------------------------------------------------------
    | Run:  SHOW COLUMNS FROM service_requests WHERE Field = 'status';
    | then set the two constants below to the matching allowed values.
    | If a value here isn't in the column's allowed set, the SR status simply
    | isn't changed (no error) — the punch lifecycle still drives the UI.
    */
    private const SR_STATUS_IN_PROGRESS = 'in_progress';
    private const SR_STATUS_QC_REVIEW   = 'qc_review';

    /**
     * Show the punch in / out page for a given service request.
     * Worker == the SR's assigned User (role ML). All data from DB.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['assignedUser.role', 'assignedUser.serviceDomains', 'client', 'project', 'category']);

        $worker = $serviceRequest->assignedUser;
        abort_if(!$worker, 404, 'No worker assigned to this service request.');

        // Optional: ensure only the assigned worker (or staff) can view.
        // abort_unless(Auth::id() === $worker->id || Auth::user()?->isWorker() === false, 403);

        $punch = $serviceRequest->activePunch();

        if (!$punch) {
            $punch = new Punch([
                'service_request_id' => $serviceRequest->id,
                'user_id'            => $worker->id,
                'status'             => 'draft',
                'materials_subtotal' => 0,
                'labour_charge'      => 0,
                'grand_total'        => 0,
            ]);
        } else {
            $punch->load('items');
        }

        return view('worker.punch', [
            'sr'     => $serviceRequest,
            'worker' => $worker,
            'punch'  => $punch,
            'items'  => $punch->exists ? $punch->items : collect(),
        ]);
    }

    /** Punch IN — start time, description, start photo, GPS. */
    public function punchIn(Request $request, ServiceRequest $serviceRequest)
    {
        $worker = $serviceRequest->assignedUser;
        abort_if(!$worker, 404, 'No worker assigned.');

        $data = $request->validate([
            'site_location'    => ['required', 'string', 'max:255'],
            'work_description' => ['required', 'string'],
            'start_photo'      => ['required', 'image', 'max:8192'],
            'start_gps_lat'    => ['nullable', 'numeric'],
            'start_gps_lng'    => ['nullable', 'numeric'],
        ]);

        $path = $request->file('start_photo')->store('punches/start', 'public');

        Punch::create([
            'service_request_id' => $serviceRequest->id,
            'user_id'            => $worker->id,
            'punch_in_at'        => now(),
            'site_location'      => $data['site_location'],
            'work_description'   => $data['work_description'],
            'start_photo_path'   => $path,
            'start_gps_lat'      => $data['start_gps_lat'] ?? null,
            'start_gps_lng'      => $data['start_gps_lng'] ?? null,
            'status'             => 'punched_in',
        ]);

        // Reflect progress on the SR — only if the value is one your
        // service_requests.status column actually allows (prevents ENUM truncation).
        $this->safeSetSrStatus($serviceRequest, self::SR_STATUS_IN_PROGRESS);

        return redirect()
            ->route('worker.punch.show', $serviceRequest)
            ->with('ok', 'Punched in successfully.');
    }

    /** Save equipment + billing as a draft without submitting. */
    public function saveDraft(Request $request, ServiceRequest $serviceRequest)
    {
        $punch = $serviceRequest->activePunch();
        abort_if(!$punch, 400, 'Punch in first.');

        $this->syncBilling($request, $punch);

        return redirect()
            ->route('worker.punch.show', $serviceRequest)
            ->with('ok', 'Draft saved.');
    }

    /** Punch OUT / final submit — completion photo, summary, sign-off. */
    public function submit(Request $request, ServiceRequest $serviceRequest)
    {
        $punch = $serviceRequest->activePunch();
        abort_if(!$punch, 400, 'Punch in first.');

        $data = $request->validate([
            'completion_summary' => ['required', 'string'],
            'finish_photo'       => ['required', 'image', 'max:8192'],
            'finish_gps_lat'     => ['nullable', 'numeric'],
            'finish_gps_lng'     => ['nullable', 'numeric'],
            'customer_name'      => ['required', 'string', 'max:255'],
            'customer_phone'     => ['nullable', 'string', 'max:32'],
        ]);

        $path = $request->file('finish_photo')->store('punches/finish', 'public');

        DB::transaction(function () use ($request, $serviceRequest, $punch, $data, $path) {
            $this->syncBilling($request, $punch);

            $punch->update([
                'punch_out_at'       => now(),
                'finish_photo_path'  => $path,
                'finish_gps_lat'     => $data['finish_gps_lat'] ?? null,
                'finish_gps_lng'     => $data['finish_gps_lng'] ?? null,
                'completion_summary' => $data['completion_summary'],
                'customer_name'      => $data['customer_name'],
                'customer_phone'     => $data['customer_phone'] ?? null,
                'status'             => 'submitted',
            ]);

            // Move SR to QC review — only if the column allows the value.
            $this->safeSetSrStatus($serviceRequest, self::SR_STATUS_QC_REVIEW);
        });

        return redirect()
            ->route('worker.punch.show', $serviceRequest)
            ->with('ok', 'Punch out submitted. Moved to QC review.');
    }

    /**
     * Update service_requests.status only if the given value is actually
     * allowed by the column. Works whether the column is an ENUM or a plain
     * string, and never throws a truncation error.
     */
    private function safeSetSrStatus(ServiceRequest $sr, string $status): void
    {
        static $allowed = null;

        if ($allowed === null) {
            $allowed = $this->serviceRequestStatusValues();
        }

        // If we couldn't read an ENUM list (e.g. it's a VARCHAR), $allowed is
        // an empty array — in that case just write the value directly.
        if ($allowed === [] || in_array($status, $allowed, true)) {
            $sr->update(['status' => $status]);
        }
        // else: silently skip — punch lifecycle still drives the stepper.
    }

    /** Read the allowed ENUM values for service_requests.status, or [] if not an ENUM. */
    private function serviceRequestStatusValues(): array
    {
        try {
            $row = DB::selectOne("SHOW COLUMNS FROM service_requests WHERE Field = 'status'");
            if ($row && preg_match('/^enum\((.*)\)$/i', $row->Type, $m)) {
                return array_map(
                    fn ($v) => trim($v, "'"),
                    str_getcsv($m[1])
                    
                );
            }
        } catch (\Throwable $e) {
            // Ignore — fall through to "write directly".
        }
        return [];
    }

    /**
     * Rebuild line items + recompute totals.
     * Expects arrays: item_name[], item_qty[], item_rate[], plus labour_charge.
     */
    private function syncBilling(Request $request, Punch $punch): void
    {
        $names = $request->input('item_name', []);
        $qtys  = $request->input('item_qty', []);
        $rates = $request->input('item_rate', []);

        $subtotal = 0;
        $rows = [];

        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $qty  = (float) ($qtys[$i] ?? 0);
            $rate = (float) ($rates[$i] ?? 0);
            $line = round($qty * $rate, 2);
            $subtotal += $line;

            $rows[] = ['name' => $name, 'qty' => $qty, 'rate' => $rate, 'line_total' => $line];
        }

        $labour = (float) $request->input('labour_charge', 0);

        $punch->items()->delete();
        if ($rows) {
            $punch->items()->createMany($rows);
        }

        $punch->update([
            'materials_subtotal' => $subtotal,
            'labour_charge'      => $labour,
            'grand_total'        => $subtotal + $labour,
            'receipt_number'     => $request->input('receipt_number'),
            'notes'              => $request->input('notes'),
        ]);
    }
}