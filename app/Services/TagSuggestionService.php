<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Support\Facades\Cache;

/**
 * Suggests the best category and skills for an initiative from its description.
 *
 * The description vector is compared (cosine similarity) with a vector for every
 * category and skill. Category/skill vectors are created once and cached.
 */
class TagSuggestionService
{
    private const MAX_SKILLS = 3;

    public function __construct(private GeminiEmbeddingService $gemini)
    {
    }

    /**
     * @return array{category: array{id:int, name:string, score:float}, skills: array<array{id:int, name:string, score:float}>}
     */
    public function suggest(string $title, string $description): array
    {
        $vector = $this->gemini->embedQuery(trim("Volunteer initiative: {$title}.\nDescription: {$description}"));

        $categories = Category::orderBy('name')->get();
        $skills = Skill::orderBy('name')->get();

        $categoryVectors = $this->labelVectors(
            $categories->map(fn (Category $c) => "Cause: {$c->name}. {$c->description}")->all()
        );

        $skillVectors = $this->labelVectors(
            $skills->map(fn (Skill $s) => "Volunteer skill: {$s->name}")->all()
        );

        $bestCategory = $categories->values()
            ->map(fn (Category $c, int $i) => [
                'id'    => $c->category_id,
                'name'  => $c->name,
                'score' => $this->cosine($vector, $categoryVectors[$i]),
            ])
            ->sortByDesc('score')
            ->first();

        $bestSkills = $skills->values()
            ->map(fn (Skill $s, int $i) => [
                'id'    => $s->skill_id,
                'name'  => $s->name,
                'score' => $this->cosine($vector, $skillVectors[$i]),
            ])
            ->sortByDesc('score')
            ->take(self::MAX_SKILLS)
            ->values()
            ->all();

        return ['category' => $bestCategory, 'skills' => $bestSkills];
    }

    /** One batch request for all labels, then cached forever (changes only if the labels change). */
    private function labelVectors(array $texts): array
    {
        $key = 'gemini:labels:'.sha1(config('services.gemini.embed_model').'|'.config('services.gemini.embed_dim').'|'.implode("\n", $texts));

        return Cache::rememberForever($key, fn () => $this->gemini->embedMany($texts, GeminiEmbeddingService::TASK_DOCUMENT));
    }

    /** cos θ = (A · B) / (|A| × |B|) */
    private function cosine(array $a, array $b): float
    {
        $dot = $normA = $normB = 0.0;

        foreach ($a as $i => $value) {
            $dot += $value * $b[$i];
            $normA += $value * $value;
            $normB += $b[$i] * $b[$i];
        }

        return ($normA > 0 && $normB > 0) ? $dot / (sqrt($normA) * sqrt($normB)) : 0.0;
    }
}