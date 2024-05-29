<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Task extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['user_id', 'parent_id', 'status', 'priority', 'title', 'description', 'created_at', 'completed_at',];
}
