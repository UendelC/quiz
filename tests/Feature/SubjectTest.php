<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testParticipantsCanHaveManySubjects()
    {
        $user = User::factory()->create();
        $subject = Subject::factory()->create();

        $subject->participants()->attach($user->id);

        $this->assertTrue($subject->participants()->exists($user->id));
    }
}
