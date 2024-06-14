<?php

namespace App\Dto;

use App\Enums\PriorityEnum;

readonly class TaskUpdateDto
{
    public function __construct(
        public ?int          $parentId,
        public ?string       $title,
        public ?string       $description,
        public ?PriorityEnum $priority,
    ) {
    }

    public static function fromArray(array $data): TaskUpdateDto
    {
        return new self(
            parentId: $data['parent_id'],
            title: $data['title'],
            description: $data['description'],
            priority: $data['priority'],
        );
    }
}
