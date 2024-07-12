<?php

declare(strict_types = 1);

namespace App\Dto;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;

readonly class TaskFiltersDto
{

    public string $title;
    public string $description;
    public PriorityEnum $priority;
    public StatusEnum $status;

    public function __construct(
        string       $title,
        string       $description,
        PriorityEnum $priority = PriorityEnum::LOW,
        StatusEnum   $status = StatusEnum::TODO,
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
    }
}
