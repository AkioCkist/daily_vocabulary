<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RealVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('real_words.json'); // <-- fixed path

        if (!File::exists($path)) {
            throw new \Exception("Missing data file: real_words.json");
        }

        $words = json_decode(File::get($path), true);

        // Optional: clear table before seeding
        DB::table('words')->truncate();

        $insertData = [];

        foreach ($words as $item) {
            $insertData[] = [
                'word'          => $item['word'],
                'pronunciation' => $item['pronunciation'],
                'definition'    => $item['definition'],
                'meaning'       => $item['meaning'],
                'example'       => $item['example'],
                'topic'         => $item['topic'],
                'cefr_level'    => $item['cefr_level'],
                'source'        => $item['source'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // Prevent duplicate key errors
        DB::table('words')->insertOrIgnore($insertData);
    }
}
