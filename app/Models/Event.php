<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * A volunteer initiative published by an organisation.
 */
class Event extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'org_id',
        'category_id',
        'title',
        'description',
        'requirements',
        'location',
        'state',
        'event_date',
        'start_time',
        'end_time',
        'application_deadline',
        'capacity',
        'image_path',
        'is_featured',
        'status',
    ];

    // The 768-number AI vector is never sent to pages or JSON.
    protected $hidden = ['embedding'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'application_deadline' => 'date',
            'capacity' => 'integer',
            'is_featured' => 'boolean',
            'embedded_at' => 'datetime',
        ];
    }

    /* ---------------- Relationships ---------------- */

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'event_skill', 'event_id', 'skill_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'event_id', 'event_id');
    }

    public function approvedSubmissions(): HasMany
    {
        return $this->submissions()->where('review_status', Submission::STATUS_APPROVED);
    }

    /* ---------------- Query scopes ---------------- */

    public function scopePublished(Builder $query): void
    {
        $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->whereDate('event_date', '>=', today());
    }

    /** Published, not yet happened, and the application deadline has not passed. */
    public function scopeOpenForApplications(Builder $query): void
    {
        $query->published()
            ->upcoming()
            ->where(function (Builder $q) {
                $q->whereNull('application_deadline')
                  ->orWhereDate('application_deadline', '>=', today());
            });
    }

    /* ---------------- Helpers ---------------- */

    /** Last day volunteers can apply. */
    public function closingDate(): Carbon
    {
        return $this->application_deadline ?? $this->event_date;
    }

    /** Remaining volunteer slots, or null when capacity is unlimited. */
    public function slotsLeft(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        $approved = $this->approved_submissions_count
            ?? $this->approvedSubmissions()->count();

        return max(0, $this->capacity - $approved);
    }

    public function isFull(): bool
    {
        return $this->slotsLeft() === 0;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->closingDate()->copy()->endOfDay()->isFuture()
            && ! $this->isFull();
    }

    /** "Weekends" or "Weekdays", used by the availability part of Smart Match. */
    public function dayType(): string
    {
        return $this->event_date->isWeekend() ? 'Weekends' : 'Weekdays';
    }

    /** "9:00 AM – 1:00 PM" */
    public function timeRange(): string
    {
        $start = Carbon::parse($this->start_time)->format('g:i A');

        return $this->end_time
            ? $start.' – '.Carbon::parse($this->end_time)->format('g:i A')
            : $start;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}