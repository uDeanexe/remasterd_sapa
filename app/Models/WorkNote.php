<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkNote extends Model
{
    protected $fillable = [
        'user_id',
        'note_date',
        'title',
        'body',
        'tags',
    ];

    protected $casts = [
        'note_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
