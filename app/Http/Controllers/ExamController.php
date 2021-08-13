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
            $exam = $user
                ->lecture
                ->exams()
                ->with(['category', 'users'])
                ->get()
                ->map(
                    function ($exam) {
                        $exam->category_name = $exam->category->name;
                        $exam->creation_date = $exam->created_at->format('d/m/Y');
                        $exam->actions = isset($exam->users);

                        return $exam;
                    }
                );
            return $exam;
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
                'questions' => 'required',
                'title' => 'required',
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
                'title' => $request->title,
                'published' => false,
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
