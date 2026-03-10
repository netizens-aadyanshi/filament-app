<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonComment extends Model
{

    protected $fillable = [
        'lesson_id', 'author_name', 'body', 'is_approved'
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    protected $casts = [
        'is_approved' => 'boolean',
    ];
}
