<?php
namespace App\Enums;

enum PaymentMethod:string {
    case PAYMOB = 'paymob';
    case PAYPAL = 'paypal';
    case COD = 'cash_on_delivery';
}

