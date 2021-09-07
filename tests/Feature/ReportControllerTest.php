<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUsersCanGetAFullReport()
    {
        $this->withoutExceptionHandling();
        $teacher = User::factory()->teacher()->create();
        $participant = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $subject = Subject::factory()->create(
            ['teacher_id' => $teacher->id]
        );
        $category = Category::factory()->create();

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $exam->users()->attach($participant->id, ['score' => 10]);
        $exam->users()->attach($another_participant->id, ['score' => 8]);

        $another_exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $another_exam->users()->attach($participant->id, ['score' => 4]);
        $another_exam->users()->attach($another_participant->id, ['score' => 0]);

        Sanctum::actingAs($teacher);

        $this->json('POST', 'api/report')
            ->assertStatus(200)
            ->assertJson(
                [
                    'mean_score' => 5.5,
                    'standard_deviation' => 3.84,
                    'scores' => [
                        '10.0',
                        '8.0',
                        '4.0',
                        '0.0',
                    ],
                ]
            );

    }

    public function testUsersCanGetReportWithParticipantsFilter()
    {
        $this->withoutExceptionHandling();
        $teacher = User::factory()->teacher()->create();
        $participant = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $subject = Subject::factory()->create(
            ['teacher_id' => $teacher->id]
        );

        $category = Category::factory()->create();
        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $exam->users()->attach($participant->id, ['score' => 10]);
        $exam->users()->attach($another_participant->id, ['score' => 8]);

        $another_exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $another_exam->users()->attach($participant->id, ['score' => 4]);
        $another_exam->users()->attach($another_participant->id, ['score' => 0]);

        Sanctum::actingAs($teacher);
        $this->json('POST', 'api/report', ['participants' => [$participant->id]])
            ->assertStatus(200)
            ->assertJson(
                [
                    'mean_score' => 7.0,
                    'standard_deviation' => 3.0,
                    'scores' => [
                        '10.0',
                        '4.0',
                    ],
                ]
            );
    }

    public function testUsersCanGetReportWithCategoryFilter()
    {
        $this->withoutExceptionHandling();
        $teacher = User::factory()->teacher()->create();

        $participant = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $category = Category::factory()->create();
        $another_category = Category::factory()->create();

        $subject = Subject::factory()->create(
            ['teacher_id' => $teacher->id]
        );

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );
        $exam->users()->attach($participant->id, ['score' => 10]);
        $exam->users()->attach($another_participant->id, ['score' => 8]);

        $another_exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $another_category->id,
            ]
        );
        $another_exam->users()->attach($participant->id, ['score' => 4]);
        $another_exam->users()->attach($another_participant->id, ['score' => 0]);

        Sanctum::actingAs($teacher);

        $this->json('POST', 'api/report', ['categories' => [$category->id]])
            ->assertStatus(200)
            ->assertJson(
                [
                    'mean_score' => 9.0,
                    'standard_deviation' => 1.0,
                    'scores' => [
                        '10.0',
                        '8.0',
                    ],
                ]
            );
    }

    public function testUsersCanGetReportWithExamsFilter()
    {
        $teacher = User::factory()->teacher()->create();

        $participant = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $subject = Subject::factory()->create(
            ['teacher_id' => $teacher->id]
        );

        $category = Category::factory()->create();

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $exam->users()->attach($participant->id, ['score' => 10]);
        $exam->users()->attach($another_participant->id, ['score' => 8]);

        $another_exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $another_exam->users()->attach($participant->id, ['score' => 4]);
        $another_exam->users()->attach($another_participant->id, ['score' => 0]);

        Sanctum::actingAs($teacher);

        $this->json('POST', 'api/report', ['exams' => [$exam->id]])
            ->assertStatus(200)
            ->assertJson(
                [
                    'mean_score' => 9.0,
                    'standard_deviation' => 1.0,
                    'scores' => [
                        '10.0',
                        '8.0',
                    ],
                ]
            );
    }

    public function testUsersCanGetReportWithDateFilter()
    {
        $teacher = User::factory()->teacher()->create();

        $participant = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $another_date = Carbon::now()->subDays(15);

        $subject = Subject::factory()->create(
            ['teacher_id' => $teacher->id]
        );

        $category = Category::factory()->create();

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
            ]
        );

        $another_exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category->id,
                'created_at' => $another_date,
            ]
        );

        $exam->users()->attach($participant->id, ['score' => 10]);
        $exam->users()->attach($another_participant->id, ['score' => 8]);
        $another_exam->users()->attach($participant->id, ['score' => 4]);
        $another_exam->users()->attach($another_participant->id, ['score' => 0]);

        Sanctum::actingAs($teacher);

        $this->json(
            'POST',
            'api/report',
            [
                'start_date' => $another_date->subDay()->format('Y-m-d'),
                'end_date' => $another_date->addDays(2)->format('Y-m-d'),
            ]
        )
            ->assertStatus(200)
            ->assertJson(
                [
                    'mean_score' => 2.00,
                    'standard_deviation' => 2.00,
                    'scores' => [
                        '4.0',
                        '0.0',
                    ],
                ]
            );
    }

}
