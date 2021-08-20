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
                ->with(['category', 'users', 'questions.choices'])
                ->get()
                ->map(
                    function ($exam) {
                        $exam->category_name = $exam->category->name;
                        $exam->creation_date = $exam->created_at->format('d/m/Y');
                        $exam->actions = $exam->users->count() == 0;

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

        $teacher = auth()->user();

        $exam = Exam::create(
            [
                'category_id' => $category->id,
                'subject_id' => $teacher->lecture->id,
                'title' => $request->title,
                'published' => false,
            ]
        );

        foreach ($questions as $question) {
            $saved_question = Question::create(
                [
                    'title' => $question['title'],
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

    public function update(Request $request, Exam $exam)
    {
        if ($exam->users()->count() == 0) {
            if (isset($request['form'])) {
                $exam->update(
                    ['title' => $request['form']['title']]
                );

                $exam->questions()->each(
                    function ($question_to_delete_choice) {
                        $question_to_delete_choice->choices()->each(
                            function ($choice_to_dissociate) {
                                $choice_to_dissociate
                                    ->question()
                                    ->dissociate()
                                    ->save();
                            }
                        );
                    }
                );

                $exam->questions()->detach();

                if (gettype($request['form']['category']) == 'string') {
                    $category = Category::create(
                        [
                            'name' => $request['form']['category'],
                        ]
                    );

                    $exam->category()->associate($category);

                    $exam->save();
                } elseif (gettype($request['form']['category']) == 'integer') {
                    $exam->category()->associate(
                        Category::findOrFail($request['form']['category'])
                    );
                    $exam->save();
                }

                foreach ($request['form']['questions'] as $question) {
                    if (isset($question['id'])) {
                        $question_to_update = Question::findOrFail($question['id']);
                        $question_to_update->update(
                            [
                                'title' => $question['title'],
                                'explanation' => $question['explanation'],
                            ]
                        );

                        $exam->questions()->attach($question_to_update->id);
                    } else {
                        $new_question = Question::create(
                            [
                                'title' => $question['title'],
                                'explanation' => $question['explanation'],
                            ]
                        );

                        $question['id'] = $new_question->id;

                        $exam->questions()->attach($new_question);
                    }

                    foreach ($question['choices'] as $choice) {
                        if (isset($choice['id'])) {
                            $choice_to_update = Choice::findOrFail($choice['id']);
                            $choice_to_update->update(
                                [
                                    'description' => $choice['description'],
                                    'is_right' => $choice['is_right'],
                                    'question_id' => $question['id'],
                                ]
                            );

                            $question_to_associate = Question::findOrFail(
                                $question['id']
                            );
                            $choice_to_update
                                ->question()
                                ->associate($question_to_associate->id)
                                ->save();
                        } else {
                            $new_choice = Choice::create(
                                [
                                    'description' => $choice['description'],
                                    'is_right' => $choice['is_right'],
                                    'question_id' => $question['id'],
                                ]
                            );

                            $question_to_associate = Question::findOrFail(
                                $question['id']
                            );

                            $new_choice
                                ->question()
                                ->associate($question_to_associate->id)
                                ->save();
                        }
                    }
                }
            } else {
                $exam->update(
                    [
                        'published' => $request->published,
                    ]
                );
            }

            return response()->json(
                [
                    'status' => 'ok',
                ]
            );
        }

        return response()->json(
            [
                'status' => 'Não pode atualizar exames já respondidos',
            ]
        );
    }

    public function destroy(Exam $exam)
    {
        if ($exam->users()->count() == 0) {
            $exam->delete();

            return response()->json(
                [
                    'status' => 'ok',
                ]
            );
        }

        return response()->json(
            [
                'status' => 'Não pode atualizar exames já respondidos',
            ]
        );
    }

    public function show(String $exam_id)
    {
        $exam = Exam::find($exam_id);
        return new ExamResource($exam);
    }
}
