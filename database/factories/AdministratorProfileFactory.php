<?php

namespace Database\Factories;

use App\Models\AdministratorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministratorProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdministratorProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'role_id' => 2
        ];
    }
}
