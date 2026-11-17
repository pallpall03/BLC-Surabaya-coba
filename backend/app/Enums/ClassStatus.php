<?php

namespace App\Enums;

enum ClassStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Running = 'running';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
}
