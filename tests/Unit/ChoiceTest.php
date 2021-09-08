<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChoiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAChoiceBelongsToAQuestion()
    {
        $question = Question::factory()->create();
        $choice = Choice::factory()->create(
            [
                'question_id' => $question->id,
            ]
        );

        $this->assertEquals($choice->question->id, $question->id);
        $this->assertCount(1, $question->choices()->get());
        $this->assertTrue($question->choices()->exists($choice->id));
    }
}
