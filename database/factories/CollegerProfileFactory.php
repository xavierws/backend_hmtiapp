<?php

namespace Database\Factories;

use App\Models\CollegerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollegerProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CollegerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'nrp' => $this->faker->unique()->numberBetween(5211840000001, 5211840000300),
            'birthday' => $this->faker->date('Y-m-d'),
            'address' => $this->faker->address,
            'role_id' => 1
        ];
    }
}
