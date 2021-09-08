<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->exam = Exam::factory()->create(
            [
                'category_id' => Category::factory()->create()->id,
                'subject_id' => Subject::factory()->create(
                    [
                        'teacher_id' => User::factory()->teacher()->create()->id,
                    ]
                )->id,
            ]
        );
    }

    public function testAQuestionCanHaveAnExam()
    {
        $question = Question::factory()->create();

        $question->exams()->attach($this->exam->id);

        $this->assertEquals(1, $question->id);
        $this->assertCount(1, Question::all());
        $this->assertEquals($this->exam->id, $question->exams()->first()->id);
    }

    public function testAQuestionCanHaveMultipleChoices()
    {
        $question = Question::factory()->create();
        $choices = Choice::factory(4)->create(
            [
                'question_id' => $question->id,
            ]
        );

        $this->assertEquals($choices->pluck('id'), $question->choices->pluck('id'));
    }
}
