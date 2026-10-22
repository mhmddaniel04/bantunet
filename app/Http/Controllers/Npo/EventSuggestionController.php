<?php

namespace App\Http\Controllers\Npo;

use App\Exceptions\GeminiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\SuggestTagsRequest;
use App\Services\TagSuggestionService;
use Illuminate\Http\JsonResponse;

/**
 * Returns the AI-suggested category and skills as JSON for the initiative form.
 */
class EventSuggestionController extends Controller
{
    public function __invoke(SuggestTagsRequest $request, TagSuggestionService $tags): JsonResponse
    {
        try {
            $result = $tags->suggest(
                (string) $request->validated('title'),
                $request->validated('description'),
            );
        } catch (GeminiException $e) {
            report($e);

            return response()->json([
                'message' => 'AI suggestions are unavailable right now. Please choose the category and skills manually.',
            ], 503);
        }

        $percent = fn (float $score) => (int) round(max(0, $score) * 100);

        return response()->json([
            'category' => [
                'id'    => $result['category']['id'],
                'name'  => $result['category']['name'],
                'score' => $percent($result['category']['score']),
            ],
            'skills' => array_map(fn (array $skill) => [
                'id'    => $skill['id'],
                'name'  => $skill['name'],
                'score' => $percent($skill['score']),
            ], $result['skills']),
        ]);
    }
}