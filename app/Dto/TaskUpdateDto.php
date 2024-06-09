<?php

namespace App\Dto;

use App\Enums\PriorityEnum;

class TaskUpdateDto
{
    public function __construct(
        public readonly ?int $parent_id,
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?PriorityEnum $priority,
    ) {
    }

    public static function fromArray(array $data): TaskUpdateDto
    {
        return new self(
            parent_id: $data['parent_id'],
            title: $data['title'],
            description: $data['description'],
            priority: $data['priority'],
        );
    }
}
