<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(
            [
                'category' => 'required',
                'question' => 'required',
                'explanation' => 'required',
            ]
        );

        $exam = Exam::create();

        $category = Category::create(
            [
                'name' => $request['category'],
            ]
        );

        $question = Question::create(
            [
                'title' => $request['question'],
                'explanation' => $request['explanation'],
                'exam_id' => $exam->id,
                'category_id' => $category->id,
            ]
        );

        foreach ($request['choices'] as $choice) {
            Choice::create(
                [
                    'question_id' => $question->id,
                    'description' => $choice['description'],
                    'is_right' => $choice['is_right'],
                ]
            );
        }

        return response()->json([
            'status' => 'ok',
            'exam' => $exam->toArray(),
        ]);
    }
}
