<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSubjectCanHaveManyParticipants()
    {
        $user = User::factory()->create();
        $another_user = User::factory()->create();

        $subject = Subject::factory()->create();

        $subject->participants()->attach($user->id);
        $subject->participants()->attach($another_user->id);

        $this->assertEquals(2, $subject->participants()->count());
        $this->assertTrue($subject->participants()->exists($user->id));
    }

    public function testSubjectAlwaysHaveATeacher()
    {
        $teacher = User::factory()->teacher()->create();
        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $this->assertNotNull($subject->teacher_id);
        $this->assertEquals($teacher->id, $subject->teacher->id);
    }

    public function testSubjectCanHaveMultipleExams()
    {
        $subject = Subject::factory()->create();

        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );
        Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        $this->assertTrue($subject->exams()->exists($exam->id));
        $this->assertCount(2, $subject->exams()->get());
    }
}
