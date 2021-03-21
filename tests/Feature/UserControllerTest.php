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
        $user = User::factory()->create(
            [
                'name' => 'Uendel',
            ]
        );
        
        Sanctum::actingAs(
            $user
        );
    
        $response = $this->get('api/user');
        $response->assertOk();
        $response->assertJson([$user->toArray()]);
    }

    public function testCanStoreANewUser()
    {
        $user_data = [
            'name' => 'Uendel',
            'phone' => '99999999',
            'email' => 'uendel@gmail.com',
            'password' => '123456',
        ];
          
          $response = $this->post('api/users', $user_data);

          $response->assertOk();
          $user = User::firstOrFail();
          $response->assertJSON($user);
    }
}
