<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerCommission extends Model
{
    protected $casts = [
        'amount' => 'datetime',
    ];
}