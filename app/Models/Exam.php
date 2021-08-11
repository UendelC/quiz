<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
