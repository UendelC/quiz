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
use Revolution\Google\Sheets\Facades\Sheets;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $previous_exams = $user->exams()->pluck('id');

        $exam = Exam::whereNotIn('id', $previous_exams)->latest()->first();

        if (!isset($exam)) {
            return [
                'message' => 'No exams available',
            ];
        }

        return new ExamResource($exam);
    }

    public function store(Request $request)
    {
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
            ]
        );

        foreach ($questions as $question) {
            $saved_question = Question::create(
                [
                    'title' => $question['question'],
                    'explanation' => $question['explanation'],
                    'exam_id' => $exam->id,
                ]
            );

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

        $grade = ($score / $amount_of_questions) * 10;

        $user->exams()->attach(
            $exam_id,
            [
                'score' => $grade,
            ]
        );

        $exam = Exam::find($exam_id);

        $data[] = [
            'Topico' => $exam->category->name,
            'participante' => $user->name,
            'Nota' => $grade,
            'date' => $exam->created_at->format('d/m/Y'),
        ];

        // Sheets::spreadsheet('1SRGZH4PaHn-w52GI1ZwdTCJsjVLundz4rPN4A66k4Yg')
        //     ->sheet('Página3')
        //     ->append($data);

        return response()->json(
            [
                'status' => 'ok',
                'score' => $grade,
            ]
        );
    }

    public function grades()
    {
        $user = auth()->user();

        $exams = $user
            ->exams()
            ->with('category')
            ->get()
            ->map(
                function ($exam) {
                    $exam->score = $exam->pivot->score;
                    $exam->date = $exam->created_at->format('d/m/Y');
                    $exam->category_name = $exam->category->name;
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
