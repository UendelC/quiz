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
                    $category = Category::factory()->create();
                    $user->exams()->saveMany(
                        Exam::factory(4)->make(
                            [
                                'category_id' => $category->id,
                            ]
                        )
                    );

                    $user->exams()->each(
                        function ($exam) {
                            $exam->questions()->saveMany(
                                Question::factory(5)->make(
                                    [
                                        'exam_id' => $exam->id,
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

        $new_category = Category::factory()->create();
        $new_exam = Exam::factory()->create(
            [
                'category_id' => $new_category->id,
            ]
        );
        $new_exam->questions()->saveMany(
            Question::factory(5)
                ->make(
                    [
                        'exam_id' => $new_exam->id,
                    ]
                )
        );
        $new_exam->questions()->each(
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
}
