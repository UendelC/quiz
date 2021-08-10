<?php

namespace Database\Seeders;

use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exams = Exam::all();

        $exams->each(
            function ($exam) {
                $exam->questions()->saveMany(
                    [
                        Question::factory()->make(
                            [
                                'exam_id' => $exam->id,
                            ]
                        )
                    ]
                );
            }
        );
    }
}
