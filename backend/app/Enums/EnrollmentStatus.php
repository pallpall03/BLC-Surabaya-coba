<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Registered = 'registered';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
