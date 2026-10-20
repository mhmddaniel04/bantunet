<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A registered non-profit organisation (NPO). This is the only account type
 * that logs in to BantuNet.
 */
class Organization extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'org_id';

    protected $fillable = [
        'org_name',
        'ros_number',
        'org_email',
        'org_phone',
        'org_hq_location',
        'state',
        'website',
        'description',
        'logo_path',
        'password',
    ];

    // is_verified is intentionally NOT fillable: an NPO cannot verify itself.

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'password' => 'hashed',   // automatically bcrypt-hashed when saved
        ];
    }

    /* ---------------- Relationships ---------------- */

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'org_id', 'org_id');
    }

    /** All applications to all of this organisation's events. */
    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Submission::class,
            Event::class,
            'org_id',    // events.org_id
            'event_id',  // submissions.event_id
            'org_id',    // organizations.org_id
            'event_id'   // events.event_id
        );
    }

    /* ---------------- Helpers ---------------- */

    /** Used by Laravel's password reset and mail notifications. */
    public function getEmailForPasswordReset(): string
    {
        return $this->org_email;
    }

    public function routeNotificationForMail(): string
    {
        return $this->org_email;
    }

    /** "Selangor Wildlife Protection" -> "SW" (for the avatar circle). */
    public function initials(): string
    {
        return collect(explode(' ', $this->org_name))
            ->filter()
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }
}