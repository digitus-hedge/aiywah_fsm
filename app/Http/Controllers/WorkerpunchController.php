<?php

namespace App\Http\Controllers;

use App\Models\Punch;
use App\Models\Punchitem;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\NotificationLog;
use App\Models\PunchPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WorkerpunchController extends Controller
{
   /** The signed-in technician. */
    private function worker(Request $request): User
    {
        $user = Auth::guard('worker')->user();

        abort_unless($user, 401, 'Not signed in.');

        return $user;
    }

    private function ownedRequest(Request $request, int $srId): ServiceRequest
    {
        return ServiceRequest::where('id', $srId)
            ->where('assigned_user_id', $this->worker($request)->id)
            ->firstOrFail();
    }

    /** The one open punch for this SR, or 404. */
    private function openPunch(Request $request, int $srId): Punch
    {
        $this->ownedRequest($request, $srId);

        return Punch::where('service_request_id', $srId)
            ->where('user_id', $this->worker($request)->id)
            ->whereIn('status', ['draft', 'punched_in'])
            ->latest('id')
            ->firstOrFail();
    }


    private function buildSrRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    public function punchIn(Request $request)
    {
        $data = $request->validate([
            'sr_id'            => ['required', 'integer'],
            'work_description' => ['nullable', 'string', 'max:2000'],
        ] + $this->geoRules());

        $sr     = $this->ownedRequest($request, $data['sr_id']);
        $worker = $this->worker($request);


        abort_unless($sr->accepted_at, 422, 'Accept the job before punching in.');
        $exists = Punch::where('service_request_id', $sr->id)
            ->whereIn('status', ['draft', 'punched_in'])
            ->exists();
        abort_if(
            $sr->eta_at && now()->addMinutes(15)->lt($sr->eta_at),
            422,
            'Too early — scheduled for ' . $sr->eta_at->format('d M Y, H:i')
                . '. Reschedule if you need to start now.'
        );

        $oldStatus = $sr->status;          // capture BEFORE the transaction updates it

        $punch = DB::transaction(function () use ($sr, $worker, $data, $oldStatus) {
            $p = Punch::create([
                'service_request_id' => $sr->id,
                'user_id'            => $worker->id,
                'punch_in_at'        => now(),
                'work_description'   => $data['work_description'] ?? null,
                'status'             => 'punched_in',
                'materials_subtotal' => 0,
                'labour_charge'      => 0,
                'grand_total'        => 0,
            ]  + $this->geoColumns($data, 'punch_in'));

            $sr->update([
                'status'      => 'In Progress',
                'hold_reason' => null,
                'held_at'     => null,
            ]);
            

            NotificationLog::create([
                'service_request_id' => $sr->id,
                'event'       => 'status_updated',
                'title'       => 'Status Updated',
                'message'     => $this->buildSrRef($sr) . ' work started'
                    . (optional($worker)->name ? ' by ' . $worker->name : ''),
                'from_status' => $oldStatus,   // e.g. 'Accepted'
                'to_status'   => 'In Progress',
                'caused_by'   => $worker->id,
            ]);

            return $p;
        });
        
        try {
            $wa = app(\App\Services\WhatsAppService::class);
            $wa->notifyMaintenanceStarted($sr, $punch);          // customer
            $wa->notifyInternalMaintenanceStarted($sr, $punch);  // internal
        } catch (\Throwable $e) {
            Log::error('Punch-in WhatsApp failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
        }
        return response()->json([
            'ok'          => true,
            'punch_id'    => $punch->id,
            'punch_in_at' => $punch->punch_in_at->toIso8601String(),
            'location'    => [
                'lat'     => $punch->punch_in_lat,
                'lng'     => $punch->punch_in_lng,
                'address' => $punch->punch_in_address,
            ],
        ]);
    }

    /** Compliance photo / signature upload. */
    public function upload(Request $request)
    {
        $data = $request->validate([
            'sr_id'   => ['required', 'integer'],
            'type'    => ['required', 'in:before,after'],
            'files'   => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $punch = $this->openPunch($request, $data['sr_id']);

        $column = match ($data['type']) {
            'before' => 'start_photo_path',
            'after'  => 'finish_photo_path',
        };

        $existing = $punch->photos()->where('type', $data['type'])->count();
        abort_if($existing + count($data['files']) > 10, 422, 'Maximum 10 photos per stage.');

        $saved = [];

        DB::transaction(function () use ($request, $punch, $data, $column, &$saved, $existing) {
            foreach ($request->file('files') as $i => $file) {
                $path = $file->store("punches/{$punch->id}/{$data['type']}", 'public');

                $photo = PunchPhoto::create([
                    'punch_id'   => $punch->id,
                    'type'       => $data['type'],
                    'path'       => $path,
                    'sort_order' => $existing + $i,
                ]);

                $saved[] = ['id' => $photo->id, 'url' => $photo->url];
            }

            // Mirror the first photo into the legacy column so the punch-out
            // gate and the QC/invoice views keep working.
            if (blank($punch->$column)) {
                $punch->update([$column => $punch->photos()->where('type', $data['type'])->first()->path]);
            }
        });

        return response()->json([
            'ok'     => true,
            'type'   => $data['type'],
            'photos' => $saved,
            'count'  => $punch->photos()->where('type', $data['type'])->count(),
        ]);
    }

    /** Log one material line against the open punch. */
    public function expense(Request $request)
    {
        $data = $request->validate([
            'sr_id'    => ['required', 'integer'],
            'category' => ['required', 'string', 'max:120'],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'name'     => ['nullable', 'string', 'max:190'],
            'qty'      => ['nullable', 'numeric', 'min:0.01'],
            'receipt'  => ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $punch = $this->openPunch($request, $data['sr_id']);
        $qty   = $data['qty'] ?? 1;

        // Store outside the transaction — a rolled-back write shouldn't strand a file,
        // and a failed upload shouldn't leave a half-committed item.
        $receiptPath = $request->hasFile('receipt')
            ? $request->file('receipt')->store("punches/{$punch->id}/receipts", 'public')
            : null;

        try {
            $item = DB::transaction(function () use ($punch, $data, $qty, $receiptPath) {
                $item = Punchitem::create([
                    'punch_id'     => $punch->id,
                    'name'         => $data['name'] ?? $data['category'],
                    'category'     => $data['category'],
                    'qty'          => $qty,
                    'rate'         => round($data['amount'] / $qty, 2),
                    'line_total'   => $data['amount'],
                    'receipt_path' => $receiptPath,
                    'recon_status' => 'pending',
                ]);

                $this->recalcTotals($punch);

                return $item;
            });
        } catch (\Throwable $e) {
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }
            throw $e;
        }

        $punch->refresh();

        return response()->json([
            'ok'                 => true,
            'item_id'            => $item->id,
            'receipt_url'        => $item->receipt_url,
            'materials_subtotal' => (string) $punch->materials_subtotal,
            'grand_total'        => (string) $punch->grand_total,
        ]);
    }

    /** Technician attended but could not finish — park the SR and close the punch. */
public function hold(Request $request)
{
    $data = $request->validate([
        'sr_id'  => ['required', 'integer'],
        'status' => ['required', 'in:On Hold,Reschedule'],
        'reason' => ['required', 'string', 'min:10', 'max:1000'],
        'eta_at' => ['nullable', 'date', 'after:now'],
    ] + $this->geoRules());

    $sr     = $this->ownedRequest($request, $data['sr_id']);
    $punch  = $this->openPunch($request, $sr->id);
    $worker = $this->worker($request);

    $oldStatus = $sr->status;

    DB::transaction(function () use ($punch, $sr, $data, $oldStatus, $worker) {
        // Close the punch so the SR isn't stuck with an open one.
        $punch->fill([
            'punch_out_at'       => now(),
            'completion_summary' => $data['reason'],
            'status'             => 'on_hold',
        ] + $this->geoColumns($data, 'punch_out'))->save();

        $this->recalcTotals($punch);

        $sr->update([
            'status'      => $data['status'],
            'hold_reason' => $data['reason'],
            'held_at'     => now(),
            'eta_at'      => $data['status'] === 'Reschedule'
                ? ($data['eta_at'] ?? null)
                : $sr->eta_at,
        ]);

        NotificationLog::create([
            'service_request_id' => $sr->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => $this->buildSrRef($sr) . ' — visit incomplete: '
                . \Illuminate\Support\Str::limit($data['reason'], 60),
            'from_status' => $oldStatus,          // 'In Progress'
            'to_status'   => $data['status'],
            'caused_by'   => $worker->id,
        ]);
    });

   try {
    $wa = app(\App\Services\WhatsAppService::class);
    $wa->notifyMaintenanceOnHold($sr, $sr->status, $sr->hold_reason ?? 'Further work required');
    $wa->notifyInternalMaintenanceOnHold($sr, $sr->status, $sr->hold_reason ?? 'Further work required');
} catch (\Throwable $e) {
    Log::error('On-hold WhatsApp failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
}

    return response()->json([
        'ok'     => true,
        'status' => $data['status'],
    ]);
}

    public function punchOut(Request $request)
    {
        $data = $request->validate([
            'sr_id'         => ['required', 'integer'],
            'summary'       => ['nullable', 'string', 'max:2000'],
        ] + $this->geoRules());

        $sr    = $this->ownedRequest($request, $data['sr_id']);
        $punch = $this->openPunch($request, $sr->id);

        // Server-side compliance gate. The JS lock is a convenience, not a control.
        foreach (
            [
                'start_photo_path'        => 'Before photo',
                'finish_photo_path'       => 'After photo',
                'customer_signature_path' => 'Customer signature',
            ] as $col => $label
        ) {
            abort_if(blank($punch->$col), 422, "{$label} is required before finishing.");
        }

        $oldStatus = $sr->status;
        $worker    = $this->worker($request);

        DB::transaction(function () use ($punch, $sr, $data, $oldStatus, $worker) {
            $punch->fill([
                'punch_out_at'       => now(),
                'completion_summary' => $data['summary'] ?? null,
                'status'             => 'submitted',
            ] + $this->geoColumns($data, 'punch_out'))->save();

            $this->recalcTotals($punch);

            $sr->update([
                'status'         => 'Qc Review',
                'qc_updated'     =>now(),
                'qc_reviewed_at' => null,
                'qc_reviewed_by' => null,
            ]);

            NotificationLog::create([
                'service_request_id' => $sr->id,
                'event'       => 'status_updated',
                'title'       => 'Status Updated',
                'message'     => $this->buildSrRef($sr) . 'Submitted for QC Review',
                'from_status' => $oldStatus,
                'to_status'   => 'Qc Review',
                'caused_by'   => $worker->id,
            ]);
        });

        $punch->refresh();
        app(\App\Services\WhatsAppService::class)->notifyServiceStatus($sr, 'QC Review');
        return response()->json([
            'ok'          => true,
            'duration'    => $punch->duration_label,
            'grand_total' => (string) $punch->grand_total,
        ]);
    }

    /** Client-signed acceptance PDF → customer_signature_path. */
    public function signature(Request $request)
    {
        $data = $request->validate([
            'sr_id'       => ['required', 'integer'],
            'client_name' => ['required', 'string', 'max:190'],
            'signature'   => ['required', 'file', 'max:8192', 'mimes:pdf'],
        ] + $this->geoRules());


       $punch = $this->openPunch($request, $data['sr_id']);

        $path = $request->file('signature')->store("punches/{$punch->id}/acceptance", 'public');

        $punch->update([
            'customer_signature_path' => $path,
            'customer_name'           => $data['client_name'],
            'signed_at'               => now(),
        ] + $this->geoColumns($data, 'signature'));

        return response()->json(['ok' => true, 'path' => $path]);
    }

    private function recalcTotals(Punch $punch): void
    {
        $subtotal = Punchitem::where('punch_id', $punch->id)->sum('line_total');

        $punch->forceFill([
            'materials_subtotal' => $subtotal,
            'grand_total'        => $subtotal + (float) ($punch->labour_charge ?? 0),
        ])->save();
    }
    public function deletePhoto(Request $request)
    {
        $data = $request->validate([
            'sr_id'    => ['required', 'integer'],
            'photo_id' => ['required', 'integer'],
        ]);

        $punch = $this->openPunch($request, $data['sr_id']);
        $photo = PunchPhoto::where('punch_id', $punch->id)->findOrFail($data['photo_id']);

        $type   = $photo->type;
        $column = $type === 'before' ? 'start_photo_path' : 'finish_photo_path';

        DB::transaction(function () use ($photo, $punch, $type, $column) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();

            // Re-point the legacy column at whatever is now first (or null).
            $next = $punch->photos()->where('type', $type)->first();
            $punch->update([$column => $next?->path]);
        });

        return response()->json([
            'ok'    => true,
            'count' => $punch->photos()->where('type', $type)->count(),
        ]);
    }

    /** Shared validation rules for an optional captured location. */
    private function geoRules(): array
    {
        return [
            'lat'      => ['nullable', 'numeric', 'between:-90,90'],
            'lng'      => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'address'  => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Map validated lat/lng/accuracy/address onto prefixed columns. */
    private function geoColumns(array $data, string $prefix): array
    {
        return [
            "{$prefix}_lat"      => $data['lat'] ?? null,
            "{$prefix}_lng"      => $data['lng'] ?? null,
            "{$prefix}_accuracy" => $data['accuracy'] ?? null,
            "{$prefix}_address"  => $data['address'] ?? null,
            "{$prefix}_coarse"   => $data['coarse'] ?? null,
        ];
    }
}
