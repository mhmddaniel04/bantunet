<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\MatchRequest;
use App\Models\Category;
use App\Models\Skill;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    /** The questions. Pre-filled when coming back via "Edit my answers". */
    public function index(Request $request): View
    {
        return view('public.match.index', [
            'values' => $request->only(['about', 'skills', 'category', 'availability', 'state']),
            ...$this->options(),
        ]);
    }

    public function results(MatchRequest $request, MatchingService $matching): View
    {
        return view('public.match.results', [
            'report'  => $matching->recommend($request->profile()),
            'answers' => $request->validated(),
            ...$this->options(),
        ]);
    }

    private function options(): array
    {
        return [
            'skills'       => Skill::orderBy('name')->pluck('name', 'skill_id')->all(),
            'categories'   => Category::orderBy('name')->pluck('name', 'category_id')->all(),
            'availability' => config('bantunet.availability'),
            'states'       => config('bantunet.states'),
        ];
    }
}