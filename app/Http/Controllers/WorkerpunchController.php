<?php

namespace App\Http\Controllers;

use App\Models\Punch;
use App\Models\Punchitem;
use App\Models\ServiceRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class WorkerpunchController extends Controller
{
    private function worker(Request $request): User
{
    $id = $request->input('worker') ?? session('dev_worker_id');

    if ($id) {
        return User::findOrFail((int) $id);
    }

    $mlRoleId = Role::where('code', 'ML')->value('id');
    abort_unless($mlRoleId, 500, 'ML role missing — run RoleSeeder.');

    $worker = User::where('role_id', $mlRoleId)->first();
    abort_unless($worker, 500, 'No Maintenance Lead user exists.');

    return $worker;
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

    public function punchIn(Request $request)
    {
        $data = $request->validate([
            'sr_id'            => ['required', 'integer'],
            'work_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $sr     = $this->ownedRequest($request, $data['sr_id']);
         $worker = $this->worker($request);

     
         abort_unless($sr->accepted_at, 422, 'Accept the job before punching in.');
        $exists = Punch::where('service_request_id', $sr->id)
            ->whereIn('status', ['draft', 'punched_in'])
            ->exists();
        abort_if(
            $sr->eta_at && now()->lt($sr->eta_at),
            422,
            'Too early — scheduled for ' . $sr->eta_at->format('d M Y, H:i') . '. Reschedule if you need to start now.'
        );

        $punch = DB::transaction(function () use ($sr, $worker, $data) {
           $p = Punch::create([
                'service_request_id' => $sr->id,
                'user_id'            => $worker->id,
                'punch_in_at'        => now(),
                'work_description'   => $data['work_description'] ?? null,
                'status'             => 'punched_in',
                'materials_subtotal' => 0,
                'labour_charge'      => 0,
                'grand_total'        => 0,
            ]);
            $sr->update([
                'status'      => 'In Progress',
                'hold_reason' => null,
                'held_at'     => null,
            ]);

            return $p;
        });
            app(\App\Services\WhatsAppService::class)->notifyServiceStatus($sr, 'Work in progress');
        return response()->json([
            'ok'          => true,
            'punch_id'    => $punch->id,
            'punch_in_at' => $punch->punch_in_at->toIso8601String(),
        ]);
        
    }

    /** Compliance photo / signature upload. */
    public function upload(Request $request)
    {
        $data = $request->validate([
            'sr_id' => ['required', 'integer'],
            'type'  => ['required', 'in:before,after'],
            'file'  => ['required', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $punch = $this->openPunch($request, $data['sr_id']);

        $column = match ($data['type']) {
            'before' => 'start_photo_path',
            'after'  => 'finish_photo_path',
        };

        $path = $request->file('file')->store("punches/{$punch->id}", 'public');
        $punch->update([$column => $path]);

        return response()->json(['ok' => true, 'path' => $path, 'type' => $data['type']]);
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

    public function punchOut(Request $request)
    {
        $data = $request->validate([
            'sr_id'         => ['required', 'integer'],
            'summary'       => ['nullable', 'string', 'max:2000'],
        ]);

        $sr    = $this->ownedRequest($request, $data['sr_id']);
    $punch = $this->openPunch($request, $sr->id);

        // Server-side compliance gate. The JS lock is a convenience, not a control.
        foreach ([
            'start_photo_path'        => 'Before photo',
            'finish_photo_path'       => 'After photo',
            'customer_signature_path' => 'Customer signature',
        ] as $col => $label) {
            abort_if(blank($punch->$col), 422, "{$label} is required before finishing.");
        }

        DB::transaction(function () use ($punch, $sr, $data) {
            $punch->fill([
                'punch_out_at'       => now(),
                'completion_summary' => $data['summary'] ?? null,
                'status'             => 'submitted',
            ])->save();

            $this->recalcTotals($punch);

            $sr->update([
                'status'         => 'Qc Review',
                'qc_reviewed_at' => null,
                'qc_reviewed_by' => null,
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
    ]);   


    $punch = $this->openPunch($request, $data['sr_id']);

    $path = $request->file('signature')->store("punches/{$punch->id}/acceptance", 'public');

    $punch->update([
        'customer_signature_path' => $path,
        'customer_name'           => $data['client_name'],
    ]);

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
}