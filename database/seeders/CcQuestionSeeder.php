<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CcQuestion;

class CcQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cc_questions = [
            [
                'title' => 'CC1',
                'question' => 'Which of the following best describes your awareness of a CC?',
            ],
            [
                'title' => 'CC2',
                'question' => 'If aware of CC (answered 1-3 in CC1), would say that the CC of this was...?',
            ],
            [
                'title' => 'CC3',
                'question' => 'If aware of CC (answered 1-3 in CC1), how much did the CC help you in your transaction?',
            ],

        ];

        CcQuestion::insert($cc_questions);
    }
}
