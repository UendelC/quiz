<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function process(Request $request)
    {
        $teacher = auth()->user();
        $teacher_query = $teacher->lecture->exams();

        if ($request->has('participants')) {
            dump('com participantes');
            $teacher_query = $teacher_query
                ->whereHas(
                    'users',
                    function ($user) use ($request) {
                        $user->whereIn('id', $request->participants);
                    }
                );
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

        dd($teacher_query->get()->toArray());
        return $teacher_query->get();
    }
}
