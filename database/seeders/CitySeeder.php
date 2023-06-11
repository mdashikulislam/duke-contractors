<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (CITY_LIST as $list){
            $exist = City::where('name',$list)->first();
            if (empty($exist)){
                City::create([
                    'name' => $list,
                    'shingle' => 0,
                    'tpo' => 0,
                    'metal' => 0,
                    'flat' => 0,
                    'tile' => 0,
                ]);
            }

        }
    }
}
