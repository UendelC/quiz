<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
		$user_data = [
			'name' => 'Uendel',
			'password' => '123456',
		];
		
		$response = $this->get('/user');

		$response->assertStatus(200);
	}
}
