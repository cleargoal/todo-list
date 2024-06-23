<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Engines\Engine;
use Laravel\Scout\EngineManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Method for Scout search
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->descriptio,
        ];
    }
}
