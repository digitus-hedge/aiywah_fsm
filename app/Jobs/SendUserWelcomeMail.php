<?php

namespace App\Jobs;

use App\Mail\UserWelcomeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * ShouldBeEncrypted matters here: this job carries the user's plaintext
 * password, which would otherwise sit readable in the jobs table (and in
 * failed_jobs indefinitely) until a worker picks it up.
 */
class SendUserWelcomeMail implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 3;
    public $backoff = [10, 60, 180];
    public $timeout = 60;

    public function __construct(
        public int $userId,
        public string $password,
        public string $roleName,
    ) {}

    public function handle(): void
    {
        $user = User::with('role')->find($this->userId);
        if (! $user) return;

        try {
            Mail::to($user->email)->send(
                new UserWelcomeMail($user, $this->password, $this->roleName)
            );
        } catch (\Throwable $e) {
            Log::error('User welcome mail failed', [
                'user_id' => $this->userId,
                'error'   => $e->getMessage(),
            ]);
            throw $e;   // let the queue retry it
        }
    }
}