<?php

namespace App\Services;

use App\Models\Client;
use App\Models\EmailLog;
use App\Models\ServiceRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send a mailable and log the attempt. Returns the EmailLog row.
     */
    public function sendLogged(
        ?ServiceRequest $sr,
        ?Client $client,
        string $recipient,
        string $event,
        Mailable $mailable,
        string $preview = '',
        ?string $refOverride = null
    ): ?EmailLog {
        $log = null;

        try {
            $log = EmailLog::create([
                'service_request_id' => $sr?->id,
                'client_id'          => $client?->id,
                'sr_reference'       => $sr
                    ? ('SR-' . $sr->created_at->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT))
                    : $refOverride,
                'recipient'          => $recipient,
                'client_name'        => $client?->contact_name,
                'event'              => $event,
                'status'             => EmailLog::STATUS_PENDING,
                'mailable'           => class_basename($mailable),
                'subject'            => $this->extractSubject($mailable),
                'message'            => $preview,
            ]);
        } catch (\Throwable $e) {
            Log::error('Email log row create failed', ['error' => $e->getMessage()]);
        }

        try {
            Mail::to($recipient)->send($mailable);

            $log?->update([
                'status'  => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $log?->update([
                'status' => EmailLog::STATUS_FAILED,
                'error'  => $e->getMessage(),
            ]);
            Log::error('Email send failed', [
                'log_id' => $log?->id,
                'to'     => $recipient,
                'error'  => $e->getMessage(),
            ]);
        }

        return $log?->fresh();
    }

    public function retryLog(EmailLog $log): EmailLog
    {
        // Rebuild the mailable from the stored class name + the related models.
        $class = 'App\\Mail\\' . $log->mailable;

        if (!class_exists($class)) {
            $log->update([
                'status'      => EmailLog::STATUS_FAILED,
                'error'       => "Mailable class {$class} not found",
                'retry_count' => $log->retry_count + 1,
            ]);
            return $log->fresh();
        }

        try {
            $client  = $log->client_id ? Client::find($log->client_id) : null;
            $project = null;

            // Many mailables take (Client, Project, portalUrl) - try that shape first.
            if ($client && method_exists($client, 'projects')) {
                $project = $client->projects()->oldest('id')->first();
            }

            $portalUrl = $client
                ? route('portal.client', ['code' => $client->unique_code])
                : null;

            $mailable = $project
                ? new $class($client, $project, $portalUrl)
                : new $class($client);

            Mail::to($log->recipient)->send($mailable);

            $log->update([
                'status'      => EmailLog::STATUS_SENT,
                'sent_at'     => now(),
                'error'       => null,
                'retry_count' => $log->retry_count + 1,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'status'      => EmailLog::STATUS_FAILED,
                'error'       => $e->getMessage(),
                'retry_count' => $log->retry_count + 1,
            ]);
        }

        return $log->fresh();
    }

    private function extractSubject(Mailable $mailable): ?string
    {
        try {
            // Mailable keeps subject on the envelope in Laravel 10+.
            return $mailable->envelope()->subject ?? null;
        } catch (\Throwable) {
            return null;
        }
    }
}