<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        $jobType = JOB_TYPE;
        shuffle($jobType);
        $status = LEAD_STATUE;
        shuffle($status);



        return [
            'user_id' => $this->faker->randomNumber(),
            'client_name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'job_type' => $jobType[0],
            'additional_comments' => $this->faker->word(),
            'price_of_quote' => $this->faker->numberBetween(1111,9999),
            'status' => $status[0],
            'created_at' => $this->faker->dateTimeBetween('-90 days','200 days'),
            'updated_at' => $this->faker->dateTimeBetween('-90 days','200 days'),
        ];
    }
}
