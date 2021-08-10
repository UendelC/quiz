<?php

namespace Database\Seeders;

use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Seeder;

class ChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = Question::all();

        $questions->each(
            function ($question) {
                $question->choices()->saveMany(
                    Choice::factory(4)->make(
                        [
                            'question_id' => $question->id,
                        ]
                    ),
                );
            }
        );
    }
}
