<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'score' => $this->faker->numberBetween(0, 10),
            'category_id' => Category::factory()->create()->id,
            'subject_id' => Subject::factory()->create()->id,
            'title' => $this->faker->title,
            'published' => $this->faker->boolean(),
        ];
    }
}
