<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->type == 'participant') {
            $previous_exams = $user->exams()->pluck('id');
            $exam = Exam::whereNotIn('id', $previous_exams)->latest()->first();
            return new ExamResource($exam);
        }

        if ($user->type == 'teacher') {
            $exam = $user->lecture->exams()->get();
            return ExamResource::collection($exam);
        }

        if (!isset($exam)) {
            return [
                'message' => 'No exams available',
            ];
        }
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'subject_id' => 'required',
                'questions' => 'required'
            ]
        );
        $questions = $request->questions;

        if (is_string($request->category)) {
            $category = Category::create(
                [
                    'name' => $request->category,
                ]
            );
        } else {
            $category = Category::findOrFail($request->category);
        }

        $exam = Exam::create(
            [
                'category_id' => $category->id,
                'subject_id' => $request->subject_id,
            ]
        );

        foreach ($questions as $question) {
            $saved_question = Question::create(
                [
                    'title' => $question['question'],
                    'explanation' => $question['explanation'],
                ]
            );

            $exam->questions()->attach($saved_question->id);

            foreach ($question['choices'] as $choice) {
                Choice::create(
                    [
                        'question_id' => $saved_question->id,
                        'description' => $choice['description'],
                        'is_right' => $choice['is_right'],
                    ]
                );
            }
        }

        return response()->json(
            [
                'status' => 'ok',
            ]
        );
    }
}
