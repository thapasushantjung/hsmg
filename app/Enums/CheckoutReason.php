<?php

namespace App\Enums;

enum CheckoutReason: string
{
    case PersonalChoice = 'personal_choice';
    case Eviction = 'eviction';
    case Transfer = 'transfer';
    case EndOfStay = 'end_of_stay';
    case Other = 'other';
}
