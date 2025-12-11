<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordsTableSeeder extends Seeder
{
    public function run()
    {
        $json = file_get_contents(database_path('real_words.json'));
        $words = json_decode($json, true);

        foreach ($words as $word) {
            DB::table('words')->updateOrInsert(
                ['word' => $word['word']],
                [
                    'pronunciation' => $word['pronunciation'],
                    'meaning' => $word['meaning'],
                    'definition' => $word['definition'],
                    'example' => $word['example'],
                    'topic' => $word['topic'],
                    'cefr_level' => $word['cefr_level'],
                    'source' => $word['source'],
                ]
            );
        }
    }
}
