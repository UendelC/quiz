<?php

namespace Tests\Feature;

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
        $user_data = [
            'name' => 'Uendel',
            'email' => 'uendel@gmail.com',
            'type' => 'participant',
            'password' => '123456',
        ];
          
        $response = $this->json('POST', 'api/register', $user_data);
        
        $user = User::first();
        $response->assertStatus(200);
        $response->assertJSON(
            [
                'status' => 'Success',
            ]
        );
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
}
