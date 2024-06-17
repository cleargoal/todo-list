<?php

declare(strict_types = 1);

namespace App\Enums;

enum StatusEnum: string
{
    case TODO = 'todo';
    case DONE = 'done';
}
