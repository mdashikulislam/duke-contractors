<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function categories()
    {
        return $this->hasMany(ProductCategory::class,'product_id','id');
    }
    public function category()
    {
        return $this->hasOne(ProductCategory::class,'product_id','id');
    }

    public function item()
    {
        return $this->hasOne(CompanyProduct::class,'product_id','id');
    }
    public function items()
    {
        return $this->hasMany(CompanyProduct::class,'product_id','id');
    }
}
