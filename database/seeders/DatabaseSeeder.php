<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\JobType;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
//       Lead::factory(100)->create()->each(function ($q){
//            $number = JobType::inRandomOrder()->limit(rand(1,JobType::count()))->get()->pluck('id');
//            $q->jobTypes()->sync($number);
//        });
        $this->call(AdminSeeder::class);
        $this->call(JobTypeSeeder::class);
        $this->call(CitySeeder::class);
    }
}
