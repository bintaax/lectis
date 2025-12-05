<?php

namespace App\Enum;

enum Paiement: string
{
    case CB = 'cb';
    case PAYPAL = 'paypal';
}
