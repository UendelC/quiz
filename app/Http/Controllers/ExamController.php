<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Models\Category;
use App\Models\Choice;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
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

    public function takeExam(Request $request)
    {
        $request->validate(
            [
                'exam_id' => 'required',
                'answers' => 'required',
            ]
        );

        $answers = $request->get('answers');
        $exam_id = $request->get('exam_id');

        $user = auth()->user();

        if ($user->exams()->find($exam_id)) {
            return response()->json(
                [
                    'status' => 'exame já enviado',
                ]
            );
        }

        $score = Choice::whereIn('id', $answers)->where('is_right', true)->count();
        $amount_of_questions = Exam::find($exam_id)->questions()->count();

        $grade = ($score/$amount_of_questions) * 10;

        $user->exams()->attach(
            $exam_id,
            [
                'score' => $grade,
            ]
        );

        return response()->json(
            [
                'status' => 'ok',
                'score' => $user->exams()->find($exam_id)->pivot
            ]
        );

    }

    public function grades()
    {
        $user = auth()->user();

        $exams = $user
            ->exams()
            ->get(
                [
                    'created_at',
                ]
            )
            ->map(
                function ($exam) {
                    $exam->score = $exam->pivot->score;
                    $exam->date = $exam->created_at->format('d/m/Y');
                    unset($exam->pivot);
                    return $exam;
                }
            );

        return response()->json(
            [
                'exams' => $exams,
            ]
        );
    }
}
