<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\ReportFilterRequest;
use App\Models\Category;
use App\Services\EngagementReportService;
use App\Support\Csv;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private EngagementReportService $reports)
    {
    }

    public function index(ReportFilterRequest $request): View
    {
        return view('npo.reports.index', [
            'report'     => $this->build($request),
            'filters'    => $request->filters(),
            'categories' => Category::orderBy('name')->pluck('name', 'category_id')->all(),
            'presets'    => $this->reports->presets(),
        ]);
    }

    public function export(ReportFilterRequest $request): StreamedResponse
    {
        $filename = 'engagement-report-'.$request->from()->format('Ymd').'-'.$request->to()->format('Ymd').'.csv';

        return Csv::download($filename, $this->reports->csvRows($request->user(), $this->build($request)));
    }

    public function print(ReportFilterRequest $request): View
    {
        return view('npo.reports.print', [
            'report'       => $this->build($request),
            'organization' => $request->user(),
        ]);
    }

    private function build(ReportFilterRequest $request): array
    {
        return $this->reports->build($request->user(), $request->from(), $request->to(), $request->categoryId());
    }
}