<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'is_right',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
