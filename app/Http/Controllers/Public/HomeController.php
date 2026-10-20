<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\EventCatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(EventCatalogService $catalog): View
    {
        return view('public.home', [
            'featured'   => $catalog->featured(3),
            'categories' => $catalog->categoriesWithOpenCounts(),
            'stats'      => $catalog->platformStats(),
        ]);
    }
}