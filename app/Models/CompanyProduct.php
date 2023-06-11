<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProduct extends Model
{
    protected $fillable = ['product_id','company_id','dim_covers','unit_price'];
    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
