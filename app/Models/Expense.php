<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public function otherCompanies()
    {
        return $this->hasOne(OtherCompany::class,'id','company_id');
    }
}
