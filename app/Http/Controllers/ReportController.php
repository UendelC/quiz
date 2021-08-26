<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function process(Request $request)
    {
        $teacher = auth()->user();
        $teacher_query = $teacher->lecture->exams();

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

        if ($request->has('date_from') && $request->has('date_to')) {
            dump('com datas');
            $teacher_query = $teacher_query
                ->whereDate('date', $request->date); // between
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
                    $score = $exam->users->map(
                        function ($user) {
                            return $user->pivot->score;
                        }
                    );
                    $exam->mean_score = $score->avg();
                    $exam->scores = $score->toArray();
                    return $exam;
                }
            )
            ->toArray();

        $mean_score = array_sum(array_column($report, 'mean_score')) / count($report);
        // format the mean score to 2 decimal places
        $mean_score = number_format((float)$mean_score, 2, '.', '');

        $scores = array_column($report, 'scores');
        $scores = array_merge(...$scores);

        $standard_deviation = $this->standard_deviation($scores);

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
