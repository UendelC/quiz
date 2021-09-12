<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testATeacherCanSaveASubject()
    {
        $this->withoutExceptionHandling();
        $teacher = User::factory()->teacher()->create();

        Sanctum::actingAs($teacher);

        $payload = [
            'name' => 'Subject name',
        ];

        $response = $this->json('POST', '/api/subjects', $payload);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'message' => 'Subject created successfully',
            ]
        );

        $this->assertDatabaseHas(
            'subjects',
            [
                'name' => 'Subject name',
                'teacher_id' => $teacher->id,
            ]
        );

        $teacher->refresh();

        $this->assertEquals(1, $teacher->lecture()->count());
        $this->assertEquals('Subject name', $teacher->lecture->name);
    }

    public function testParticipantsCanHaveTheListOfAllSubjects()
    {
        $teacher = User::factory()->teacher()->create();
        $participant = User::factory()->participant()->create();

        Sanctum::actingAs($participant);

        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
                'name' => 'Subject name',
            ]
        );

        $another_subject = Subject::factory()->create();

        $response = $this->json('GET', '/api/subjects');

        $response->assertStatus(200);
        $response->assertJson(
            [
                [
                    'id' => $subject->id,
                    'name' => 'Subject name',
                    'teacher_id' => $teacher->id,
                ],
                [
                    'id' => $another_subject->id,
                    'name' => $another_subject->name,
                    'teacher_id' => $another_subject->teacher_id,
                ],
            ]
        );
    }
}
