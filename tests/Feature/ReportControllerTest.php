<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
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
}
