<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadProduct extends Model
{
    public function products()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
