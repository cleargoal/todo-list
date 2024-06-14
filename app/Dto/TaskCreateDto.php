<?php

namespace App\Dto;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;

readonly class TaskCreateDto
{
    public function __construct(
        public int          $userId,
        public int          $parentId,
        public string       $title,
        public string       $description,
        public StatusEnum   $status,
        public PriorityEnum $priority,
    ) {
    }

    public static function fromArray(array $data): TaskCreateDto
    {
        return new self(
            userId: $data['user_id'],
            parentId: $data['parent_id'],
            title: $data['title'],
            description: $data['description'],
            status: $data['status'],
            priority: $data['priority'],
        );
    }
}
