<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Adapters\ReportRepository;

final class ReportController extends BaseController
{
    private ReportRepository $reports;

    public function __construct(?ReportRepository $reports = null)
    {
        $this->reports = $reports ?? new ReportRepository();
    }

    // GET /api/v1/reports/most-loaned
    public function mostLoaned(Request $request): JsonResponse
    {
        // Only administrators can access reports.
        $this->admin($request);

        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $report = $this->reports->mostLoaned($data);

        $rows = $report->map(fn ($item) => [
            'toy_id' => $item->toy_id,
            'toy_name' => $item->name,
            'category_name' => $item->category_name,
            'times_loaned' => (int) $item->total_loans,
        ])->values();

        return response()->json([
            'rows' => $rows,
            'from' => $data['from'],
            'to' => $data['to'],
            'data' => $report->map(function ($item) {
                return [
                    'toy_id' => $item->toy_id,
                    'name' => $item->name,
                    'brand' => $item->brand,
                    'reference' => $item->reference,
                    'total_loans' => (int) $item->total_loans,
                ];
            })->values(),
        ]);
    }
}
