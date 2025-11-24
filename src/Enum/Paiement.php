<?php

namespace App\Enum;

enum Paiement: string
{
    case CB = 'carte_bancaire';
    case Paypal = 'paypal';
    case ApplePay = 'apple_pay';
}
