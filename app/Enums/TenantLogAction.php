<?php

namespace App\Enums;

enum TenantLogAction: string
{
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Transferred = 'transferred';
    case Cancelled = 'cancelled';
}
