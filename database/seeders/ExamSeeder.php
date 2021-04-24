<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('type', 'participant')
            ->each(
                function ($user) {
                    $user->exams()->saveMany(
                        Exam::factory(4)->make(
                            [
                                'user_id' => $user->id,
                            ]
                        )
                    );

                    $user->exams()->each(
                        function ($exam) {
                            $category = Category::factory()->create();
                            $exam->questions()->saveMany(
                                Question::factory(5)->make(
                                    [
                                        'exam_id' => $exam->id,
                                        'category_id' => $category->id,
                                    ]
                                )
                            );

                            $exam->questions()->each(
                                function ($question) {
                                    $question->choices()->saveMany(
                                        Choice::factory(5)->make(
                                            [
                                                'question_id' => $question->id,
                                            ]
                                        )
                                    );
                                }
                            );
                        }
                    );
                }
            );
    }
}
