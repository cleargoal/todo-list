<?php

namespace App\Dto;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;

readonly class TaskFiltersDto
{
    public function __construct(
        public ?string       $title,
        public ?string       $description,
        public ?PriorityEnum $priority,
        public ?StatusEnum $status,
    ) {
    }

    public static function fromArray(array $data): TaskFiltersDto
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            priority: $data['priority'],
            status: $data['status'],
        );
    }
}
