<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CropPlot;
use App\Models\PlotObservation;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a crop plot's field log as CSV so students can analyse it for their research.
 */
class PlotObservationExportController extends Controller
{
    public function __invoke(CropPlot $plot): StreamedResponse
    {
        $filename = str($plot->name.' field log')->slug().'.csv';

        return response()->streamDownload(function () use ($plot): void {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['plot', 'crop', 'variety', 'season', 'date', 'days_after_planting', 'activity', 'metric', 'value', 'unit', 'notes', 'recorded_by']);

            $plot->observations()
                ->with('recorder:id,name')
                ->orderBy('observed_on')
                ->orderBy('id')
                ->each(function (PlotObservation $observation) use ($out, $plot): void {
                    fputcsv($out, [
                        $plot->name,
                        $plot->crop,
                        $plot->variety,
                        $plot->season,
                        $observation->observed_on->toDateString(),
                        $plot->planted_on ? (int) $plot->planted_on->diffInDays($observation->observed_on, false) : null,
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
