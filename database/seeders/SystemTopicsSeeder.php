<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Topic;

class SystemTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemTopics = [
            ['name' => 'Business', 'description' => 'Business and professional vocabulary'],
            ['name' => 'Technology', 'description' => 'Technology and computing terms'],
            ['name' => 'Travel', 'description' => 'Travel and tourism vocabulary'],
            ['name' => 'Health', 'description' => 'Health and medical terms'],
            ['name' => 'Education', 'description' => 'Educational and academic vocabulary'],
            ['name' => 'Food & Dining', 'description' => 'Food, cooking, and dining vocabulary'],
            ['name' => 'Sports', 'description' => 'Sports and fitness terminology'],
            ['name' => 'Entertainment', 'description' => 'Movies, music, and entertainment'],
            ['name' => 'Science', 'description' => 'Scientific and research vocabulary'],
            ['name' => 'Daily Life', 'description' => 'Everyday activities and common situations'],
            ['name' => 'Environment', 'description' => 'Environmental and nature-related terms'],
            ['name' => 'Finance', 'description' => 'Financial and economic vocabulary'],
            ['name' => 'Politics', 'description' => 'Political and government terminology'],
            ['name' => 'Art & Culture', 'description' => 'Art, literature, and cultural vocabulary'],
            ['name' => 'Transportation', 'description' => 'Transportation and vehicle terms'],
        ];

        foreach ($systemTopics as $topicData) {
            // Check if the new columns exist (after migration)
            $attributes = ['name' => $topicData['name']];
            $values = ['description' => $topicData['description']];
            
            if (Schema::hasColumn('topics', 'is_system')) {
                $values['is_system'] = true;
                $values['user_id'] = null;
            }
            
            Topic::firstOrCreate($attributes, $values);
        }

        $this->command->info('System topics seeded successfully!');
    }
}
