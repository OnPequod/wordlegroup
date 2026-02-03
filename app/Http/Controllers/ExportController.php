<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\ScoreExportService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        private ScoreExportService $exportService
    ) {
    }

    public function accountScoresCsv(): StreamedResponse
    {
        $user = Auth::user();
        $filename = 'wordle-scores-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($user) {
            $output = fopen('php://output', 'w');
            $this->exportService->streamAccountCsv($user, $output);
            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function groupScoresCsv(Group $group): StreamedResponse
    {
        $user = Auth::user();

        if (!$group->isAdmin($user)) {
            abort(403, 'Only group administrators can export group scores.');
        }

        $filename = 'wordle-group-' . $group->slug . '-scores-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($group) {
            $output = fopen('php://output', 'w');
            $this->exportService->streamGroupCsv($group, $output);
            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
