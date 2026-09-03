<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserAlertSchedule;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDailySummaryNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WhatsAppService $wa): void
    {
        Log::info('SendDailySummaryNotifications: STARTED', ['at' => now()->toDateTimeString()]);

        $summaryDate = Carbon::yesterday();
        $today = strtolower(now()->format('l'));

        $shouldSend = function (User $user) use ($today): bool {
            $schedule = UserAlertSchedule::where('user_id', $user->id)->first();

            $result = $schedule && $schedule->is_enabled && (bool) $schedule->{$today};

            Log::info('SendDailySummaryNotifications: shouldSend check', [
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'has_schedule' => (bool) $schedule,
                'is_enabled' => $schedule->is_enabled ?? null,
                'today'      => $today,
                'today_flag' => $schedule->{$today} ?? null,
                'result'     => $result,
            ]);

            return $result;
        };

        $roleBatches = [
            'admin' => [['AD', 'SA'], 'notifyDailySummaryAdmin'],
            'hop'   => [['HP'], 'notifyDailySummaryHop'],
            'se'    => [['SE'], 'notifyDailySummarySe'],
            'ml'    => [['ML'], 'notifyDailySummaryMl'],
        ];

        foreach ($roleBatches as $label => [$codes, $method]) {
            $users = User::whereHas('role', fn ($q) => $q->whereIn('code', $codes))->get();

            Log::info("SendDailySummaryNotifications: {$label} users found", ['count' => $users->count()]);

            foreach ($users as $user) {
                if ($shouldSend($user)) {
                    try {
                        $wa->{$method}($user, $summaryDate);
                        Log::info("SendDailySummaryNotifications: sent to {$label}", ['user_id' => $user->id]);
                    } catch (\Throwable $e) {
                        Log::error("SendDailySummaryNotifications: FAILED sending to {$label}", [
                            'user_id' => $user->id,
                            'error'   => $e->getMessage(),
                            'trace'   => $e->getTraceAsString(),
                        ]);
                    }
                }
            }
        }

        Log::info('SendDailySummaryNotifications: FINISHED', ['at' => now()->toDateTimeString()]);
    }
}