<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = ['is_estimate'];
    public static function myRole()
    {
       $auth = getAuthInfo();
       if ($auth->role != 'Admin'){
           return self::where('leads.user_id',$auth->id)->whereNotNull('leads.user_id');
       }else{
           return self::whereNotNull('leads.user_id');
       }
    }

    public function jobTypes()
    {
        return $this->belongsToMany(JobType::class,'lead_job_types');
    }

    public function cityOfPermit()
    {
        return $this->hasOne(City::class,'id','city_for_permit');
    }
}
