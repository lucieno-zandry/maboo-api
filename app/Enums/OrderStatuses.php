<?php

namespace App\Enums;

enum OrderStatuses: int
{
    case CANCELLED = 0;
    case PROCESSING = 1;
    case DELIVERED = 2;
}