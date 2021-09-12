<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanShowUsers()
    {
        $users = User::factory()->count(3)->create();

        Sanctum::actingAs($users[0]);

        $response = $this->json('GET', 'api/users');
        $response->assertOk();
        $response->assertJson($users->toArray());
    }

    public function testCanStoreANewUser()
    {
        $this->withoutExceptionHandling();
        $subject = Subject::factory()->create();
        $user_data = [
            'name' => 'Uendel',
            'email' => 'uendel@gmail.com',
            'type' => 'participant',
            'password' => '123456',
            'subject' => $subject->id,
        ];

        $response = $this->json('POST', 'api/register', $user_data);

        $response->assertStatus(200);
        $response->assertJSON(
            [
                'status' => 'Success',
            ]
        );

        $this->assertDatabaseHas(
            'users', [
                'name' => 'Uendel',
                'email' => 'uendel@gmail.com',
                'type' => 'participant',
            ]
        );

        $user = User::where('email', 'uendel@gmail.com')->get()->first();

        $this->assertEquals($user->subjects()->first()->id, $subject->id);
    }

    public function testCanShowAnUser()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', "api/me/user");
        $response->assertJsonFragment(
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }

    public function testLogoutIsPossible()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/logout');

        $response->assertJson(
            [
                'message' => 'Tokens Revoked'
            ]
        );

    }

    public function testATeacherCanGetTheirParticipants()
    {
        $this->withoutExceptionHandling();
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);
        $participant_from_teacher = User::factory()->participant()->create();
        $another_participant = User::factory()->participant()->create();

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        $exam->users()->attach($participant_from_teacher);

        Sanctum::actingAs($teacher);

        $this->json('GET', '/api/participants-from-teacher')
            ->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        [
                            'id' => $participant_from_teacher->id,
                            'name' => $participant_from_teacher->name,
                        ],
                    ],
                ]
            )
            ->assertJsonMissing(
                [
                    'data' => [
                        [
                            'id' => $another_participant->id,
                            'name' => $another_participant->name,
                        ],
                    ],
                ]
            );
    }

    public function testATeacherCanRegisterWithSubject()
    {
        $payload = [
            'name' => 'Uendel',
            'email' => 'teste@mail.com',
            'type' => 'teacher',
            'password' => '123456',
            'subject' => 'Matematica',
        ];

        $response = $this->json('POST', 'api/register', $payload);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'status' => 'Success',
            ]
        );

        $this->assertDatabaseHas(
            'users',
            [
                'name' => 'Uendel',
                'email' => 'teste@mail.com',
                'type' => 'teacher',
            ]
        );

        $this->assertDatabaseHas(
            'subjects',
            [
                'name' => 'Matematica',
            ]
        );

        $teacher = User::where('email', 'teste@mail.com')->get()->first();

        $this->assertEquals($teacher->lecture->name, 'Matematica');
    }
}
