<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $previous_exams = $user->exams()->pluck('id');
        $exam = Exam::whereNotIn('id', $previous_exams)->latest()->first();

        return new ExamResource($exam);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'category' => 'required',
                'question' => 'required',
                'explanation' => 'required',
                'choices' => 'required'
            ]
        );

        $exam = Exam::create();

        $category = Category::findOrFail($request['category']);

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

        return response()->json(
            [
                'status' => 'ok',
            ]
        );
    }
}
