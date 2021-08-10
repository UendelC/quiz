<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users_id = User::whereType('participant')->pluck('id');
        $teachers_id = User::whereType('teacher')->pluck('id');

        $first_subject = Subject::factory()->create(
            [
                'teacher_id' => $teachers_id[0],
            ]
        );

        $first_subject->participants()->attach($users_id);

        $second_subject = Subject::factory()->create(
            [
                'teacher_id' => $teachers_id[1],
            ]
        );

        $second_subject->participants()->attach($users_id);
    }
}
