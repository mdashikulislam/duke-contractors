<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function categories()
    {
        return $this->hasMany(ProductCategory::class,'product_id','id');
    }
}
