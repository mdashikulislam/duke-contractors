<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadProduct extends Model
{
    protected $fillable = ['lead_id','product_id','quantity','type','category'];
    public function products()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
