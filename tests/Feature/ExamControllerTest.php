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
            ->assertJsonFragment(
                [
                    'category_name' => $exams[0]->category->name,
                ]
            )
            ->assertStatus(200);
    }

    public function testParticipantsCanGetExams()
    {
        $teacher = User::factory()->teacher()->create();
        $participant = User::factory()->participant()->create();
        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $exam_already_taken = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        $questions = Question::factory(2)->create();
        $questions->each(
            function ($question) {
                Choice::factory(4)->create(
                    [
                        'question_id' => $question->id,
                    ]
                );
            }
        );

        $exam->questions()->attach($questions[1]);

        $exam_already_taken->questions()->attach($questions[0]);

        $participant->exams()->attach($exam_already_taken->id);

        Sanctum::actingAs($participant);

        $this->json('GET', 'api/exams')
            ->assertJsonFragment(
                [
                    'exam_id' => $exam->id,
                ]
            )
            ->assertJsonMissing(
                [
                    'exam_id' => $exam_already_taken->id,
                ]
            )
            ->assertStatus(200);
    }

    public function testATeacherCanStoreANewExam()
    {
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $payload = [
            'subject_id' => $subject->id,
            'category' => 'categoria',
            'title' => 'titulo do exame',
            'questions' => [
                [
                    'title' => 'titulo',
                    'explanation' => 'explicação',
                    'choices' => [
                        [
                            'description' => 'descrição',
                            'is_right' => 'true',
                        ],
                    ],
                ],
            ],
        ];

        Sanctum::actingAs($teacher);

        $this->json('POST', 'api/exams', $payload)
            ->assertJson(
                [
                    'status' => 'ok',
                ]
            )
            ->assertStatus(200);

        $teacher->refresh();

        $this->assertCount(1, $teacher->lecture->exams()->get());
        $this->assertCount(1, $teacher->lecture->exams()->first()->questions()->get());
    }

    public function testATeacherCanUpdateThePublishedStatusOfAnExam()
    {
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'published' => false,
            ]
        );

        Sanctum::actingAs($teacher);

        $response = $this->json('PATCH', "api/exams/$exam->id", ['published' => '1']);

        $response->assertStatus(200)
            ->assertJson(
                [
                    'status' => 'ok'
                ]
            );

        $this->assertDatabaseHas(
            (new Exam)->getTable(),
            [
                'id' => $exam->id,
                'subject_id' => $subject->id,
                'published' => '1'
            ]
        );
    }

    public function testATeacherCannotUpdateThePublishedStatusOfAnAlreadyAnsweredExam()
    {
        $teacher = User::factory()->teacher()->create();
        $participant = User::factory()->participant()->create();

        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'published' => false,
            ]
        );

        $participant->exams()->attach($exam->id);

        Sanctum::actingAs($teacher);

        $response = $this->json(
            'PATCH',
            "api/exams/$exam->id",
            ['published' => '1']
        );

        $response->assertStatus(200)
            ->assertJson(
                [
                    'status' => 'Não pode atualizar exames já respondidos'
                ]
            );

        $this->assertDatabaseMissing(
            (new Exam)->getTable(),
            [
                'id' => $exam->id,
                'subject_id' => $subject->id,
                'published' => '1'
            ]
        );
    }

    public function testATeacherCanDeleteAnExam()
    {
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'published' => false,
            ]
        );

        Sanctum::actingAs($teacher);

        $response = $this->json('DELETE', "api/exams/$exam->id");

        $response->assertStatus(200)
            ->assertJson(
                [
                    'status' => 'ok'
                ]
            );

        $this->assertDatabaseMissing(
            (new Exam)->getTable(),
            [
                'id' => $exam->id,
                'subject_id' => $subject->id,
                'published' => '1'
            ]
        );
    }

    public function testItIsPossibleToSeeAnExam()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $exam = Exam::factory()->create(
            [
                'subject_id' => Subject::factory()->create()->id,
            ]
        );

        $this->json('GET', "api/exams/$exam->id")
            ->assertJson(
                [
                    'data' => [
                        'exam_id' => $exam->id,
                        'title' => $exam->title,
                    ]
                ]
            )
            ->assertStatus(200);
    }
}
