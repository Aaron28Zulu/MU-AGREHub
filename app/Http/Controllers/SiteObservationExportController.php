<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ResearchSite;
use App\Models\SiteObservation;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a research site's data log as CSV so students can analyse it for their research.
 */
class SiteObservationExportController extends Controller
{
    public function __invoke(ResearchSite $site): StreamedResponse
    {
        $filename = str($site->name.' data log')->slug().'.csv';

        return response()->streamDownload(function () use ($site): void {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['site', 'subject', 'focus', 'method', 'period', 'date', 'study_day', 'activity', 'metric', 'value', 'unit', 'notes', 'recorded_by']);

            $site->observations()
                ->with('recorder:id,name')
                ->orderBy('observed_on')
                ->orderBy('id')
                ->each(function (SiteObservation $observation) use ($out, $site): void {
                    fputcsv($out, [
                        $site->name,
                        $site->category->label(),
                        $site->focus,
                        $site->method,
                        $site->period,
                        $observation->observed_on->toDateString(),
                        $site->started_on ? (int) $site->started_on->diffInDays($observation->observed_on, false) : null,
                        $observation->activity->label(),
                        $observation->metric,
                        $observation->value,
                        $observation->unit,
                        $observation->notes,
                        $observation->recorder?->name,
                    ]);
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
