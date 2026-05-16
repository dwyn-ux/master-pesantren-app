<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'active_gateway',
        'tripay_api_key', 'tripay_private_key', 'tripay_merchant_code', 'tripay_mode',
        'midtrans_client_key', 'midtrans_server_key', 'midtrans_is_production',
        'xendit_secret_key'
    ];
}
