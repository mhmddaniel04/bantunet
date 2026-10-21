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

    public static function unexpectedResponse(string $reason): self
    {
        return new self('Unexpected response from Gemini: '.$reason);
    }
}