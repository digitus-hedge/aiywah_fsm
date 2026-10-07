<?php

namespace App\Jobs;

use App\Mail\ClientWelcomeMail;
use App\Models\Client;
use App\Models\Project;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendClientWelcomeNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Fired when a client is first onboarded - welcome email + WhatsApp. */
    public const EVENT_WELCOME = 'welcome';

    /** Fired when a project is added to an existing client - WhatsApp only. */
    public const EVENT_PROJECT_ADDED = 'project_added';

    public $tries   = 3;
    public $backoff = [10, 60, 180];
    public $timeout = 60;

    public function __construct(
        public int $clientId,
        public ?int $projectId = null,
        public ?string $portalUrl = null,
        public string $event = self::EVENT_WELCOME,
    ) {}

    public function handle(WhatsAppService $wa): void
    {
        $client = Client::find($this->clientId);
        if (! $client) return;

        $project = $this->projectId ? Project::find($this->projectId) : null;

        match ($this->event) {
            self::EVENT_WELCOME       => $this->welcome($wa, $client, $project),
            self::EVENT_PROJECT_ADDED => $this->projectAdded($wa, $client, $project),
            default => Log::warning('Unknown client notification event', [
                'client_id' => $this->clientId,
                'event'     => $this->event,
            ]),
        };
    }

    /** Onboarding: welcome email, then the WhatsApp welcome template. */
        private function welcome(WhatsAppService $wa, Client $client, ?Project $project): void
    {
        if ($client->email) {
            app(\App\Services\EmailService::class)->sendLogged(
                null,
                $client,
                $client->email,
                'Client Welcome',
                new ClientWelcomeMail($client, $project, $this->portalUrl),
                "Welcome mail to {$client->contact_name} <{$client->email}>",
                'WELCOME-' . ($project?->id ?? '0')
            );
        } else {
            Log::warning('Client welcome mail skipped - no email', ['client_id' => $this->clientId]);
        }

        if (! $project) {
            Log::warning('Welcome WhatsApp skipped - no project', ['client_id' => $this->clientId]);
            return;
        }

        try {
            $wa->notifyClientWelcome($client, $project);
        } catch (\Throwable $e) {
            Log::error('Welcome WhatsApp failed', [
                'client_id' => $this->clientId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    private function projectAdded(WhatsAppService $wa, Client $client, ?Project $project): void
    {
        if (! $project) {
            Log::warning('Project-added WhatsApp skipped - project missing', [
                'client_id'  => $this->clientId,
                'project_id' => $this->projectId,
            ]);
            return;
        }

        if ($client->email && class_exists(\App\Mail\ProjectAddedMail::class)) {
            app(\App\Services\EmailService::class)->sendLogged(
                null,
                $client,
                $client->email,
                'Project Added',
                new \App\Mail\ProjectAddedMail($client, $project, $this->portalUrl),
                "Project {$project->project_code} added for {$client->contact_name}",
                'PROJECT-' . $project->id
            );
        }

        try {
            $wa->notifyProjectAdded($client, $project);
        } catch (\Throwable $e) {
            Log::error('Project-added WhatsApp failed', [
                'client_id'  => $this->clientId,
                'project_id' => $this->projectId,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}