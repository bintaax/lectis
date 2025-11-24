<?php

namespace App\Enum;

enum Statut: string
{
    case PAYEE = 'payee';
    case EN_ATTENTE = 'en_attente';
    case EXPEDIEE = 'expédiée';
}
