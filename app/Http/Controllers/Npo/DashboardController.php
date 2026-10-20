<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Services\NpoDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, NpoDashboardService $dashboard): View
    {
        $organization = $request->user();
        $stats = $dashboard->stats($organization);

        return view('npo.dashboard', [
            'organization' => $organization,
            'stats'        => $stats,
            'checklist'    => $dashboard->checklist($stats),
        ]);
    }
}