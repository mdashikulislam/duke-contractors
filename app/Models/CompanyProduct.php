<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProduct extends Model
{
    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
