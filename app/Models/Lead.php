<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public static function myRole()
    {
       $auth = getAuthInfo();
       if (!$auth->role == 'Admin'){
           return self::whereNotNull('user_id')->where('user_id',$auth->id);
       }else{
           return self::whereNotNull('user_id');
       }
    }
}
