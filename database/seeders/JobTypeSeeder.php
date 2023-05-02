<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (JOB_TYPE as $type){
            $exist = JobType::where('name',$type)->first();
            if (empty($exist)){
                $exist = new JobType();
                $exist->name = $type;
                $exist->save();
            }
        }
    }
}
