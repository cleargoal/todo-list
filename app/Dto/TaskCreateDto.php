<?php

namespace App\Dto;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;

class TaskCreateDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $parent_id,
        public readonly string $title,
        public readonly string $description,
        public readonly StatusEnum $status,
        public readonly PriorityEnum $priority,
    ) {
    }

    public static function fromArray(array $data)
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
