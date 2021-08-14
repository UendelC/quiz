<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testACategoryCanHaveMultipleExams()
    {
        $category = Category::factory()->create();
        $exams = Exam::factory(2)->create(
            [
                'subject_id' => Subject::factory()->create()->id,
            ]
        );
        $category->exams()->saveMany($exams);

        $this->assertTrue($category->exams()->count() == 2);
        $this->assertEquals($exams[0]->id, $category->exams()->first()->id);
    }
}
