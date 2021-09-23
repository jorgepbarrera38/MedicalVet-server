<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class OwnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Owner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'identification' => $this->faker->unique()->numberBetween(1120582052, 1140993515),
            'names' => $this->faker->name,
            'surnames' => $this->faker->name,
            'address' => $this->faker->name,
            'phonenumber' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'city_id' => 1,
            'user_id' => 1
        ];
    }
}
