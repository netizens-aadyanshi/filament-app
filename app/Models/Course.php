<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title', 'description', 'category', 'level',
        'is_published', 'thumbnail', 'price'
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2', 
    ];
}
