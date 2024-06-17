<?php

declare(strict_types = 1);

namespace App\Enums;

enum PriorityEnum: string
{
    case HIGH = '1';
    case MIDHIGH = '2';
    case MID = '3';
    case MIDLOW = '4';
    case LOW = '5';
}
