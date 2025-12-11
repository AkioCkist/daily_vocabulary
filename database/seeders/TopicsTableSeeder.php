<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Word;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        // Get all unique topics from words table
        $wordTopics = Word::select('topic')->distinct()->pluck('topic')->toArray();
        
        // Merge with topics from JSON file
        $json = file_get_contents(database_path('real_words.json'));
        $words = json_decode($json, true);
        $jsonTopics = collect($words)->pluck('topic')->unique()->toArray();
        
        // Combine all topics
        $allTopics = array_unique(array_merge($wordTopics, $jsonTopics));
        
        // Seed all topics as system topics
        foreach ($allTopics as $topic) {
            if (!empty($topic)) {
                DB::table('topics')->updateOrInsert(
                    ['name' => $topic],
                    ['is_system' => true]
                );
            }
        }
    }
}

