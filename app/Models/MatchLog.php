<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Anonymous record of one Smart AI Match request (for evaluation).
 */
class MatchLog extends Model
{
    public const UPDATED_AT = null;   // table only has created_at

    protected $primaryKey = 'match_log_id';

    protected $fillable = [
        'query_text',
        'skill_ids',
        'category_id',
        'availability',
        'state',
        'results',
        'response_ms',
    ];

    protected function casts(): array
    {
        return [
            'skill_ids' => 'array',
            'results' => 'array',
            'response_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}