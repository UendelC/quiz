<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function process(Request $request)
    {
        dump('aqui');
        $teacher = auth()->user();
        $teacher_query = $teacher->lecture->exams();

        if ($request->has('participants')) {
            dump('com participantes');
            $teacher_query = $teacher_query
                ->map(
                    function ($exam) use ($request) {
                        $exam
                            ->users()
                            ->whereIn('id', $request->participants);
                        
                        return $exam;
                    }
                );

            dd($teacher_query);
                // ->whereHas(
                //     'users',
                //     function ($user) use ($request) {
                //         $user->whereIn('id', $request->participants);
                //     }
                // );
        }

        if ($request->has('categories')) {
            dump('com categorias');
            $teacher_query = $teacher_query
                ->whereHas(
                    'category',
                    function ($category) use ($request) {
                        $category->whereIn('id', $request->categories);
                    }
                );
        }

        if ($request->has('exams')) {
            dump('com exames');
            $teacher_query = $teacher_query
                ->whereIn('id', $request->exams);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            dump('com datas');
            $teacher_query = $teacher_query
                ->whereDate('date', $request->date); // between
        }

        $report = $teacher_query
            ->with('users')
            ->get()
            ->map(
                function ($exam) {
                    $score = $exam->users->map(
                        function ($user) {
                            return $user->pivot->score;
                        }
                    );
                    $exam->mean_score = $score->avg();
                    $exam->scores = $score->toArray();
                    $exam->standard_deviation = $this->standardDeviation($score->toArray());
                    return $exam;
                }
            )
            ->toArray();

        dd($report);

        return response()->json(
            [
                'mean_score' => $report['mean_score'],
                'standard_deviation' => $report['standard_deviation'],
                'scores' => $report['scores'],
            ]
        );
    }

    private function standardDeviation(array $scores)
    {
        $mean = array_sum($scores) / count($scores);
        $variance = array_sum(
            array_map(
                function ($x) use ($mean) {
                    return pow($x - $mean, 2);
                },
                $scores
            )
        ) / count($scores);

        return sqrt($variance);

    }
}
