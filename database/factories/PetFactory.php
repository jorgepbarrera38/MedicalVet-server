<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_id' => \App\Models\Owner::inRandomOrder()->first()->id,
            'name' => $this->faker->unique()->name,
            'date_born' => '1992-10-10',
            'sex' => $this->faker->randomElement(['MACHO', 'HEMBRA']),
            'species_id' => \App\Models\Species::inRandomOrder()->first()->id,
            'race_id' => \App\Models\Race::inRandomOrder()->first()->id,
            'color' => $this->faker->text(15),
            'size' => $this->faker->randomElement(['GRANDE', 'MEDIANO', 'PEQUEÑO']),
            'observations' => $this->faker->text(30),
            'user_id' => \App\Models\User::inRandomOrder()->first()->id
        ];
    }
}
