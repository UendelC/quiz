<?php

namespace Database\Factories;

use App\Models\Choice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Choice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(),
            'is_right' => $this->faker->boolean(),
        ];
    }
}
