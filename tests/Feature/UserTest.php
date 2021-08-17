<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAParticipantCanHaveMultipleExams()
    {
        $participant = User::factory()->participant()->create();
        $exam = Exam::factory()->create();
        $participant->exams()->save($exam);
        $this->assertTrue($participant->exams()->count() == 1);
        $this->assertEquals($participant->exams()->first()->id, $exam->id);
        $this->assertEquals($exam->users()->first()->id, $participant->id);
    }

    public function testAParticipantCanHaveMultipleSubjects()
    {
        $participant = User::factory()->participant()->create();

        $subject = Subject::factory()->create();
        $another_subject = Subject::factory()->create();

        $participant->subjects()->save($subject);
        $participant->subjects()->save($another_subject);

        $this->assertTrue($participant->subjects()->count() == 2);
        $this->assertEquals($participant->subjects()->first()->id, $subject->id);
        $this->assertEquals(
            $subject->id,
            $participant->subjects()->first()->id
        );
    }

    public function testATeacherIsTheLecurerOfTheSubject()
    {
        $teacher = User::factory()->teacher()->create();
        $subject = Subject::factory()->create(
            [
                'teacher_id' => $teacher->id,
            ]
        );

        $this->assertEquals($teacher->lecture->id, $subject->id);
        $this->assertEquals($teacher->id, $subject->teacher->id);
    }
}
