<?php

namespace App\Services;

use App\Exceptions\GeminiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Turns text into a vector (list of 768 numbers) using Google Gemini.
 * Texts with similar meaning get vectors pointing in similar directions.
 */
class GeminiEmbeddingService
{
    public const TASK_DOCUMENT = 'RETRIEVAL_DOCUMENT';   // initiative profiles
    public const TASK_QUERY = 'RETRIEVAL_QUERY';         // volunteer answers

    private const MAX_BATCH = 100;          // Gemini limit per batch request
    private const MAX_QUOTA_WAITS = 5;      // terminal only: how many times to wait for the per-minute limit
    private const MAX_WAIT_SECONDS = 90;

    /** One text → one vector. */
    public function embed(string $text, string $taskType = self::TASK_DOCUMENT): array
    {
        $response = $this->post(':embedContent', $this->requestBody($text, $taskType));

        return $this->checkVector($response->json('embedding.values'));
    }

    /** Same as embed(), but remembers the answer for a day (saves API quota). */
    public function embedQuery(string $text): array
    {
        $key = 'gemini:query:'.sha1($this->model().'|'.$this->dimensions().'|'.$text);

        return Cache::remember($key, now()->addDay(), fn () => $this->embed($text, self::TASK_QUERY));
    }

    /** Many texts → many vectors, in as few requests as possible. */
    public function embedMany(array $texts, string $taskType = self::TASK_DOCUMENT): array
    {
        $vectors = [];

        foreach (array_chunk(array_values($texts), self::MAX_BATCH) as $chunk) {
            $response = $this->post(':batchEmbedContents', [
                'requests' => array_map(
                    fn (string $text) => ['model' => 'models/'.$this->model()] + $this->requestBody($text, $taskType),
                    $chunk
                ),
            ]);

            foreach ((array) $response->json('embeddings') as $item) {
                $vectors[] = $this->checkVector($item['values'] ?? null);
            }
        }

        if (count($vectors) !== count($texts)) {
            throw GeminiException::unexpectedResponse('expected '.count($texts).' vectors, got '.count($vectors));
        }

        return $vectors;
    }

    /** [0.1, -0.2, ...] → "[0.1,-0.2,...]" (the format pgvector understands) */
    public static function toVector(array $values): string
    {
        return '['.implode(',', $values).']';
    }

    /* ------------------------------------------------------------------ */

    private function requestBody(string $text, string $taskType): array
    {
        return [
            'content'              => ['parts' => [['text' => $text]]],
            'taskType'             => $taskType,
            'outputDimensionality' => $this->dimensions(),
        ];
    }

    /**
     * Sends the request.
     * - Network problems and Google server errors (5xx) are retried quickly.
     * - Rate limit (429): terminal commands wait for the limit to reset and continue;
     *   web pages stop immediately so visitors never wait (the page shows a fallback).
     * - Daily quota used up: stop with a clear message.
     */
    private function post(string $action, array $body): Response
    {
        $key = config('services.gemini.key');

        if (blank($key)) {
            throw GeminiException::missingKey();
        }

        $url = rtrim(config('services.gemini.base_url'), '/').'/models/'.$this->model().$action;

        for ($waits = 0; ; $waits++) {
            try {
                return Http::withHeaders(['x-goog-api-key' => $key])
                    ->acceptJson()
                    ->asJson()
                    ->timeout(config('services.gemini.timeout', 20))
                    ->retry(3, 800, fn (Throwable $e) => $this->isTemporary($e))
                    ->post($url, $body)
                    ->throw();
            } catch (ConnectionException $e) {
                throw GeminiException::requestFailed($e);
            } catch (RequestException $e) {
                if ($e->response->status() !== 429) {
                    throw GeminiException::requestFailed($e);
                }

                $daily = $this->isDailyQuota($e->response);

                if ($daily || ! app()->runningInConsole() || $waits >= self::MAX_QUOTA_WAITS) {
                    throw GeminiException::quotaExceeded($daily);
                }

                $seconds = $this->retryDelaySeconds($e->response);
                logger()->info("Gemini rate limit reached, waiting {$seconds}s before continuing.");
                sleep($seconds);
            }
        }
    }

    /** Quick retry only for problems that fix themselves in a second (network, Google busy). */
    private function isTemporary(Throwable $e): bool
    {
        return $e instanceof ConnectionException
            || ($e instanceof RequestException && in_array($e->response->status(), [500, 502, 503, 504], true));
    }

    /** Google says how long to wait, e.g. "retryDelay": "37s". */
    private function retryDelaySeconds(Response $response): int
    {
        foreach ((array) $response->json('error.details') as $detail) {
            if (isset($detail['retryDelay'])) {
                return min(self::MAX_WAIT_SECONDS, max(5, (int) ceil((float) $detail['retryDelay']) + 2));
            }
        }

        return 60;
    }

    /** The error names the quota that was hit, e.g. "...RequestsPerDayPerProject...". */
    private function isDailyQuota(Response $response): bool
    {
        return str_contains($response->body(), 'PerDay');
    }

    private function checkVector(mixed $values): array
    {
        if (! is_array($values) || count($values) !== $this->dimensions()) {
            throw GeminiException::unexpectedResponse('vector must have '.$this->dimensions().' numbers');
        }

        return $values;
    }

    private function model(): string
    {
        return config('services.gemini.embed_model');
    }

    private function dimensions(): int
    {
        return (int) config('services.gemini.embed_dim');
    }
}