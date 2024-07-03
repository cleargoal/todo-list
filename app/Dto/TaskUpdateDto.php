<?php

declare(strict_types = 1);

namespace App\Dto;

use App\Enums\PriorityEnum;

class TaskUpdateDto
{
    public ?int $parentId;
    public ?string $title;
    public ?string $description;
    public ?PriorityEnum $priority;

    public function __construct(
        ?int $parentId = null,
        ?string $title = null,
        ?string $description = null,
        ?PriorityEnum $priority = null
    ) {
        $this->parentId = $parentId;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
    }
}
