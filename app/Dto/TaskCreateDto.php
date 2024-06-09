<?php

namespace App\Dto;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;

readonly class TaskCreateDto
{
    public function __construct(
        public int          $user_id,
        public int          $parent_id,
        public string       $title,
        public string       $description,
        public StatusEnum   $status,
        public PriorityEnum $priority,
    ) {
    }

    public static function fromArray(array $data): TaskCreateDto
    {
        return new self(
            user_id: $data['user_id'],
            parent_id: $data['parent_id'],
            title: $data['title'],
            description: $data['description'],
            status: $data['status'],
            priority: $data['priority'],
        );
    }
}
