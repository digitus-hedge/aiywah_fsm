<?php

namespace App\Support;

use App\Models\Project;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\URL;

class PortalLink
{
        public static function project(Project $project, ?ServiceRequest $focus = null): string
    {
        $link = URL::signedRoute('portal.project', array_filter([
            'code' => $project->project_code,
            'sr'   => $focus?->id,
        ]));

        dd($link); // ← TEMP DEBUG

        return $link;
    }

    public static function feedback(ServiceRequest $sr): string
    {
        return URL::signedRoute('clients.feedback.show', ['id' => $sr->id]);
    }

    public static function customerNumber(ServiceRequest $sr): ?string
    {
        $client  = $sr->client;
        $project = $sr->project;

        $candidates = [
            [$project?->engineer_country, $project?->engineer_contact],
            [$client?->primary_country,   $client?->primary_mobile],
        ];

        foreach ($candidates as [$cc, $number]) {
            if ($number) {
                return preg_replace('/\D+/', '', ($cc ?? '') . $number);
            }
        }

        return null;
    }
}