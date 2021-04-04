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
          
        $response = $this->json('POST', 'api/registration', $user_data);
        
        $user = User::first();
        $response->assertStatus(201);
        $response->assertJSON(
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }

    public function testCanShowAnUser()
    {
        $user = User::factory()->create();

        $response = $this->json('GET', 'api/user/' . $user->id);
        $response->assertJson(
            [
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
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
