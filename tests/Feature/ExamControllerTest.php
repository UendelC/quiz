<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTeachersCanGetExams()
    {
        $teacher = User::factory()->teacher()->create();
        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $questions = Question::factory(5)->create();
        $questions->each(
            function ($question) {
                Choice::factory(4)->create(
                    [
                        'question_id' => $question->id,
                    ]
                );
            }
        );

        $exams = Exam::factory(5)->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        foreach ($exams as $idx => $exam) {
            $exam->questions()->attach($questions[$idx]);
        }

        Sanctum::actingAs($teacher);

        $this->json('GET', 'api/exams')
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'exam_id',
                            'category' => [
                                'name',
                                'id',
                            ],
                            'questions' => [
                                '*' => [
                                    'id',
                                    'title',
                                    'explanation',
                                    'choices' => [
                                        '*' => [
                                            'description',
                                            'id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->assertStatus(200);
    }
}
