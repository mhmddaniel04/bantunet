<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $primaryKey = 'skill_id';

    protected $fillable = ['name', 'slug'];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_skill', 'skill_id', 'event_id');
    }
}