<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\StatusEnum;
use App\Enums\PriorityEnum;

class TaskCreateDto
{
    public int $userId;
    public ?int $parentId;
    public string $title;
    public string $description;
    public StatusEnum $status;
    public PriorityEnum $priority;

    public function __construct(
        int          $userId,
        string       $title,
        string       $description,
        StatusEnum   $status = StatusEnum::TODO,
        PriorityEnum $priority = PriorityEnum::LOW,
        ?int         $parentId = null,
    )
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->priority = $priority;
        $this->parentId = $parentId;
    }
}
