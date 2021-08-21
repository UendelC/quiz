<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testATeacherCanGetTheirCategories()
    {
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);
        $category_from_teacher = Category::factory()->create();
        $another_category = Category::factory()->create();

        Exam::factory(2)->create(
            [
                'subject_id' => $subject->id,
                'category_id' => $category_from_teacher->id,
            ]
        );

        Sanctum::actingAs($teacher);

        $this->json('GET', '/api/categories-from-teacher')
            ->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        [
                            'id' => $category_from_teacher->id,
                            'name' => $category_from_teacher->name,
                        ],
                    ],
                ]
            )
            ->assertJsonMissing(
                [
                    'data' => [
                        [
                            'id' => $another_category->id,
                            'name' => $another_category->name,
                        ],
                    ],
                ]
            );
    }
}
