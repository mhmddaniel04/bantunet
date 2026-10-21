<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Builds CSV downloads that open correctly in Microsoft Excel.
 */
final class Csv
{
    /**
     * @param  iterable<array>  $rows  Every row is an array of cells (the first row is usually the header).
     */
    public static function download(string $filename, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // UTF-8 "BOM" so Excel shows Malay/Chinese/Tamil names correctly.
            fwrite($out, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($out, array_map([self::class, 'safe'], $row), ',', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Stops "formula injection": a cell like =HYPERLINK(...) typed into a form
     * would otherwise run as a formula when the NPO opens the file in Excel.
     */
    private static function safe(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }
}