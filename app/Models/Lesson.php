<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'slug', 'content', 'video_url',
        'duration_minutes', 'order', 'is_free', 'is_published'
    ];

    /**
     * Boot logic for the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonComments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
    ];
}
