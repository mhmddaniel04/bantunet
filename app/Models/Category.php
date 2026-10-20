<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $primaryKey = 'category_id';

    protected $fillable = ['name', 'slug', 'description'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id', 'category_id');
    }
}