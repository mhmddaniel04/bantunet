<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Anything that goes wrong while talking to the Gemini API.
 */
class GeminiException extends RuntimeException
{
    public static function missingKey(): self
    {
        return new self('GEMINI_API_KEY is empty. Add your key to .env and run "php artisan config:clear".');
    }

    public static function requestFailed(Throwable $previous): self
    {
        return new self('Gemini API request failed: '.$previous->getMessage(), 0, $previous);
    }

    public static function quotaExceeded(bool $daily): self
    {
        return new self($daily
            ? 'Gemini free daily quota is used up. It resets at midnight Pacific Time (around 3–4 PM Malaysia time). '
              .'Run the command again after that — items already done are kept.'
            : 'Gemini rate limit reached (too many requests per minute). Wait a minute and try again.');
    }

    public static function unexpectedResponse(string $reason): self
    {
        return new self('Unexpected response from Gemini: '.$reason);
    }
}