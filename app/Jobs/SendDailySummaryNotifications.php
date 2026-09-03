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

class SendDailySummaryNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WhatsAppService $wa): void
    {
        $summaryDate = Carbon::yesterday();

        // 'monday', 'tuesday', ... matches UserAlertSchedule::DAYS column names.
        $today = strtolower(now()->format('l'));

        $scheduledFor = function (User $user) use ($today): bool {
            $schedule = UserAlertSchedule::where('user_id', $user->id)->first();
            return $schedule ? (bool) $schedule->{$today} : true; // default: every day
        };

        User::whereHas('role', fn ($q) => $q->whereIn('code', ['AD', 'SA']))
            ->get()
            ->each(function (User $user) use ($wa, $summaryDate, $scheduledFor) {
                if ($scheduledFor($user)) {
                    $wa->notifyDailySummaryAdmin($user, $summaryDate);
                }
            });

        User::whereHas('role', fn ($q) => $q->where('code', 'HP'))
            ->get()
            ->each(function (User $user) use ($wa, $summaryDate, $scheduledFor) {
                if ($scheduledFor($user)) {
                    $wa->notifyDailySummaryHop($user, $summaryDate);
                }
            });

        User::whereHas('role', fn ($q) => $q->where('code', 'SE'))
            ->get()
            ->each(function (User $user) use ($wa, $summaryDate, $scheduledFor) {
                if ($scheduledFor($user)) {
                    $wa->notifyDailySummarySe($user, $summaryDate);
                }
            });

        User::whereHas('role', fn ($q) => $q->where('code', 'ML'))
            ->get()
            ->each(function (User $user) use ($wa, $summaryDate, $scheduledFor) {
                if ($scheduledFor($user)) {
                    $wa->notifyDailySummaryMl($user, $summaryDate);
                }
            });
    }
}