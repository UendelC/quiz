<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAnExamCanHaveMultipleUsers()
    {
        $exam = Exam::factory()->create();
        $users = User::factory(2)->participant()->create();

        $exam->users()->attach($users);

        $this->assertEquals(2, $exam->users()->count());
        $this->assertTrue($exam->users()->exists($users[0]->id));
        $this->assertEquals($users[0]->exams()->first()->id, $exam->id);
    }

    public function testAnExamCanHaveMultipleQuestions()
    {
        $exam = Exam::factory()->create();
        $questions = Question::factory(2)->create();

        $exam->questions()->attach($questions);

        $this->assertEquals(2, $exam->questions()->count());
        $this->assertTrue($exam->questions()->exists($questions[0]->id));
        $this->assertEquals($questions[0]->exams()->first()->id, $exam->id);
    }

    public function testAnExamCanHaveACategory()
    {
        $category = Category::factory()->create();
        $exam = Exam::factory()->create(
            [
                'category_id' => $category->id,
            ]
        );

        $this->assertNotNull($exam->category);
        $this->assertEquals($category->id, $exam->category->id);
        $this->assertEquals($category->exams()->first()->id, $exam->id);
    }

    public function testAnExamCanHaveASubject()
    {
        $subject = Subject::factory()->create();
        $exam = Exam::factory()->create(
            [
                'subject_id' => $subject->id,
            ]
        );

        $this->assertNotNull($exam->subject);
        $this->assertEquals($subject->id, $exam->subject->id);
        $this->assertEquals($subject->exams()->first()->id, $exam->id);
    }
}
