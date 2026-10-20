<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A volunteer's application to an event.
 */
class Submission extends Model
{
    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_WITHDRAWN = 'Withdrawn';

    public const SOURCE_BROWSE = 'browse';
    public const SOURCE_SMART_MATCH = 'smart_match';

    protected $primaryKey = 'submission_id';

    protected $fillable = [
        'event_id',
        'volunteer_name',
        'volunteer_email',
        'volunteer_phone',
        'age',
        'gender',
        'tshirt_size',
        'availability',
        'emergency_name',
        'emergency_phone',
        'skills_summary',
        'resume_path',
        'resume_original_name',
        'source',
        'match_score',
    ];

    // review_status, reviewer_note, attended, hours_volunteered are set
    // explicitly by the NPO, never mass-assigned from a public form.

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'match_score' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'attended' => 'boolean',
            'hours_volunteered' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Every new application gets a unique tracking code automatically.
        static::creating(function (Submission $submission) {
            $submission->tracking_code ??= static::generateTrackingCode();
        });
    }

    /* ---------------- Relationships ---------------- */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /* ---------------- Attributes ---------------- */

    /** Always store email in lowercase so tracking lookups match. */
    protected function volunteerEmail(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtolower(trim($value)),
        );
    }

    /* ---------------- Helpers ---------------- */

    /** e.g. "BN-7K2QX9" (no 0/O/1/I to avoid confusion). */
    public static function generateTrackingCode(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = 'BN-';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    public function isPending(): bool
    {
        return $this->review_status === self::STATUS_PENDING;
    }

    /** CSS classes for the status badge, e.g. "badge badge--approved". */
    public function statusBadgeClass(): string
    {
        return 'badge badge--'.strtolower($this->review_status);
    }
}