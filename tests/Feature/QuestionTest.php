<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAQuestionCanBeAdded()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/api/questions', ['title' => 'titulo da questão']);

        $this->assertCount(1, Question::all());
    }
}
