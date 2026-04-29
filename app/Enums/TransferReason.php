<?php

namespace App\Enums;

enum TransferReason: string
{
    case Upgrade = 'upgrade';
    case Maintenance = 'maintenance';
    case RequestedChange = 'requested_change';
    case Other = 'other';
}
