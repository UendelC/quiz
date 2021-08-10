<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ExamSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(ChoiceSeeder::class);
    }
}
