<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function indexTeacher()
    {
        $user = auth()->user();

        $categories = $user
            ->lecture
            ->exams()
            ->with(['category:id,name'])
            ->get();

        dd($categories->toArray());
        return CategoryResource::collection($categories);
    }
}
