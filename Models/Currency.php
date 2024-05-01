<?php

namespace App\Modules\ConvertCurrency\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'currency_code',
        'exchange_rate',
        'iso_country',
        'updated_at',
    ];
}
