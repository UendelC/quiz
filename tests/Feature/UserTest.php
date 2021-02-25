<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanShowAnUser()
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
        $response->assertJson($user->toArray());
    }
}
