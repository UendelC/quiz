<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function process(Request $request)
    {
        $teacher = auth()->user();
        $teacher_query = $teacher->lecture->exams()->has('users');

        if ($request->has('categories')) {
            $teacher_query = $teacher_query
                ->whereHas(
                    'category',
                    function ($category) use ($request) {
                        $category->whereIn('id', $request->categories);
                    }
                );
        }

        if ($request->has('exams')) {
            $teacher_query = $teacher_query
                ->whereIn('id', $request->exams);
        }

        if ($request->has('start_date')) {
            $teacher_query = $teacher_query
                ->where(
                    'created_at',
                    '>=',
                    $request->start_date
                );
        }

        if ($request->has('end_date')) {
            $teacher_query = $teacher_query
                ->where(
                    'created_at',
                    '<=',
                    $request->end_date
                );
        }

        $report = $teacher_query
            ->when(
                $request->has('participants'), function ($query) use ($request) {
                    $query->with(
                        'users',
                        function ($query) use ($request) {
                            $query->whereIn('id', $request->participants);
                        }
                    );
                }
            )
            ->when(
                !$request->has('participants'), function ($query) use ($request) {
                    $query->with(
                        'users'
                    );
                }
            )
            ->get()
            ->map(
                function ($exam) {
                    $scores = $exam->users->map(
                        function ($user) {
                            return [
                                $user->pivot->score,
                                $user->pivot->created_at->format('d/m/Y'),
                            ];
                        }
                    );

                    $aux = [];
                    foreach ($scores as $score) {
                        $aux[] = $score[0];
                    }

                    $exam->scores = $aux;

                    if (count($exam->scores) !== 0) {
                        $exam->mean_score = array_sum($exam->scores)
                            / count($exam->scores);
                    } else {
                        $exam->mean_score = 0;
                    }
                    $exam->scores = $scores->toArray();
                    return $exam;
                }
            )
            ->filter(
                function ($exam) {
                    return $exam->users->count() !== 0;
                }
            )
            ->toArray();

        if (count($report) > 0) {
            $mean_score = array_sum(array_column($report, 'mean_score'))
                / count($report);
        } else {
            $mean_score = 0;
        }
        // format the mean score to 2 decimal places
        $mean_score = number_format((float)$mean_score, 2, '.', '');

        $scores = array_column($report, 'scores');
        $scores_notes = array_map(
            function ($scores) {
                return array_column($scores, 0);
            },
            $scores
        );
        $scores_notes = array_merge(...$scores_notes);
        $scores = array_merge(...$scores);
        array_multisort(array_column($scores, 1), SORT_ASC, $scores);

        $standard_deviation = $this->standard_deviation($scores_notes);

        $standard_deviation = number_format($standard_deviation, 2, '.', '');

        return response()->json(
            [
                'mean_score' => $mean_score,
                'standard_deviation' => $standard_deviation,
                'scores' => $scores,
            ]
        );
    }

    private function standard_deviation(array $scores)
    {
        if (count($scores) > 0) {
            $mean = array_sum($scores) / count($scores);
            $variance = array_sum(
                array_map(
                    function ($x) use ($mean) {
                        return pow($x - $mean, 2);
                    },
                    $scores
                )
            ) / count($scores);
        } else {
            $mean = 0;
            $variance = 0;
        }

        return sqrt($variance);
    }
}
