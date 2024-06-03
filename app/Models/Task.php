<?php

namespace App\Models;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['user_id', 'parent_id', 'status', 'priority', 'title', 'description', 'completed_at',];

    protected $casts = [
        'status' => StatusEnum::class,
        'priority' => PriorityEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
