<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WordsWithFilteringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create comprehensive vocabulary dataset with filtering capabilities
        $words = [
            // Technology Words
            ['word' => 'algorithm', 'pronunciation' => '/ˈælɡərɪðəm/', 'definition' => 'A set of rules or instructions for solving a problem', 'example' => 'The algorithm processes data efficiently.', 'source' => 'oxford', 'topic' => 'Technology', 'cefr_level' => 'C1', 'meaning' => 'A systematic method or procedure for solving computational problems'],
            ['word' => 'smartphone', 'pronunciation' => '/ˈsmɑːrtfoʊn/', 'definition' => 'A mobile phone with advanced features', 'example' => 'I use my smartphone to check emails.', 'source' => 'cambridge', 'topic' => 'Technology', 'cefr_level' => 'B1', 'meaning' => 'A portable device combining phone and computer functions'],
            ['word' => 'software', 'pronunciation' => '/ˈsɔːftwer/', 'definition' => 'Computer programs and applications', 'example' => 'The new software improves productivity.', 'source' => 'merriam-webster', 'topic' => 'Technology', 'cefr_level' => 'B2', 'meaning' => 'Digital programs that run on computers or devices'],
            
            // Business Words
            ['word' => 'entrepreneur', 'pronunciation' => '/ˌɑːntrəprəˈnɜːr/', 'definition' => 'A person who starts and runs a business', 'example' => 'The entrepreneur launched a successful startup.', 'source' => 'oxford', 'topic' => 'Business', 'cefr_level' => 'C1', 'meaning' => 'Someone who creates and manages business ventures'],
            ['word' => 'budget', 'pronunciation' => '/ˈbʌdʒɪt/', 'definition' => 'A plan for spending money', 'example' => 'We need to stick to our monthly budget.', 'source' => 'cambridge', 'topic' => 'Business', 'cefr_level' => 'B1', 'meaning' => 'Financial plan allocating resources for specific purposes'],
            ['word' => 'investment', 'pronunciation' => '/ɪnˈvestmənt/', 'definition' => 'Money put into something to make a profit', 'example' => 'Real estate is a good long-term investment.', 'source' => 'oxford', 'topic' => 'Business', 'cefr_level' => 'B2', 'meaning' => 'Capital allocated with expectation of future returns'],
            
            // Travel Words
            ['word' => 'passport', 'pronunciation' => '/ˈpæspɔːrt/', 'definition' => 'Official document for international travel', 'example' => 'Don\'t forget to bring your passport to the airport.', 'source' => 'cambridge', 'topic' => 'Travel', 'cefr_level' => 'A2', 'meaning' => 'Identity document required for crossing borders'],
            ['word' => 'itinerary', 'pronunciation' => '/aɪˈtɪnəreri/', 'definition' => 'A planned route or journey', 'example' => 'Our travel itinerary includes five cities.', 'source' => 'oxford', 'topic' => 'Travel', 'cefr_level' => 'B2', 'meaning' => 'Detailed plan of travel activities and destinations'],
            ['word' => 'luggage', 'pronunciation' => '/ˈlʌɡɪdʒ/', 'definition' => 'Bags and suitcases for traveling', 'example' => 'Please check your luggage at the counter.', 'source' => 'cambridge', 'topic' => 'Travel', 'cefr_level' => 'A2', 'meaning' => 'Personal belongings packed for a journey'],
            
            // Food Words
            ['word' => 'cuisine', 'pronunciation' => '/kwɪˈziːn/', 'definition' => 'A style of cooking', 'example' => 'Italian cuisine is famous worldwide.', 'source' => 'oxford', 'topic' => 'Food', 'cefr_level' => 'B2', 'meaning' => 'Traditional cooking methods and dishes of a culture'],
            ['word' => 'ingredient', 'pronunciation' => '/ɪnˈɡriːdiənt/', 'definition' => 'A component used in cooking', 'example' => 'Fresh ingredients make the best meals.', 'source' => 'cambridge', 'topic' => 'Food', 'cefr_level' => 'B1', 'meaning' => 'Individual food items combined to create dishes'],
            ['word' => 'recipe', 'pronunciation' => '/ˈresəpi/', 'definition' => 'Instructions for preparing food', 'example' => 'This recipe is easy to follow.', 'source' => 'merriam-webster', 'topic' => 'Food', 'cefr_level' => 'A2', 'meaning' => 'Step-by-step cooking instructions with ingredient list'],
            
            // Health Words
            ['word' => 'nutrition', 'pronunciation' => '/nuˈtrɪʃən/', 'definition' => 'The process of getting nutrients from food', 'example' => 'Good nutrition is essential for health.', 'source' => 'oxford', 'topic' => 'Health', 'cefr_level' => 'B2', 'meaning' => 'Science of how food affects body function and wellness'],
            ['word' => 'exercise', 'pronunciation' => '/ˈeksərsaɪz/', 'definition' => 'Physical activity for fitness', 'example' => 'Regular exercise keeps you healthy.', 'source' => 'cambridge', 'topic' => 'Health', 'cefr_level' => 'A2', 'meaning' => 'Planned physical activity to improve fitness'],
            ['word' => 'meditation', 'pronunciation' => '/ˌmedɪˈteɪʃən/', 'definition' => 'Practice of focused mental relaxation', 'example' => 'Meditation helps reduce stress.', 'source' => 'oxford', 'topic' => 'Health', 'cefr_level' => 'B2', 'meaning' => 'Mindfulness practice for mental and emotional well-being'],
            
            // Education Words
            ['word' => 'curriculum', 'pronunciation' => '/kəˈrɪkjələm/', 'definition' => 'Course of study in school', 'example' => 'The curriculum includes science and math.', 'source' => 'oxford', 'topic' => 'Education', 'cefr_level' => 'C1', 'meaning' => 'Structured academic program with specific learning objectives'],
            ['word' => 'scholarship', 'pronunciation' => '/ˈskɑːlərʃɪp/', 'definition' => 'Financial aid for students', 'example' => 'She received a scholarship to study abroad.', 'source' => 'cambridge', 'topic' => 'Education', 'cefr_level' => 'B2', 'meaning' => 'Merit-based financial assistance for educational expenses'],
            ['word' => 'homework', 'pronunciation' => '/ˈhoʊmwɜːrk/', 'definition' => 'School assignments done at home', 'example' => 'I need to finish my homework tonight.', 'source' => 'merriam-webster', 'topic' => 'Education', 'cefr_level' => 'A1', 'meaning' => 'Academic tasks assigned for completion outside classroom'],
            
            // Simple A1 words
            ['word' => 'hello', 'pronunciation' => '/həˈloʊ/', 'definition' => 'A greeting', 'example' => 'Hello, how are you?', 'source' => 'cambridge', 'topic' => 'Communication', 'cefr_level' => 'A1', 'meaning' => 'Common word used to greet someone'],
            ['word' => 'water', 'pronunciation' => '/ˈwɔːtər/', 'definition' => 'Clear liquid essential for life', 'example' => 'I drink water every day.', 'source' => 'oxford', 'topic' => 'Health', 'cefr_level' => 'A1', 'meaning' => 'H2O, the basic liquid needed for survival'],
            ['word' => 'book', 'pronunciation' => '/bʊk/', 'definition' => 'Written or printed work', 'example' => 'I love reading books.', 'source' => 'cambridge', 'topic' => 'Education', 'cefr_level' => 'A1', 'meaning' => 'Collection of written pages bound together'],
        ];

        foreach ($words as $wordData) {
            Word::updateOrCreate(
                ['word' => $wordData['word']], // Check if word already exists
                $wordData
            );
        }

        // Also create a much larger random word dataset using the factory
        Word::factory(500)->create();
    }
}
