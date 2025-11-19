<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('words')->truncate();

        $words = $this->getVocabularyData();

        foreach ($words as $word) {
            DB::table('words')->insert([
                'word' => $word['word'],
                'pronunciation' => $word['pronunciation'],
                'definition' => $word['definition'],
                'example' => $word['example'],
                'source' => $word['source'],
                'topic' => $word['topic'],
                'cefr_level' => $word['cefr_level'],
                'meaning' => $word['meaning'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeded ' . count($words) . ' vocabulary words successfully!');
    }

    /**
     * Get vocabulary data with real English words
     */
    private function getVocabularyData(): array
    {
        return [
            // Technology - A1
            [
                'word' => 'computer',
                'pronunciation' => '/kəmˈpjuːtər/',
                'definition' => 'An electronic device for storing and processing data',
                'example' => 'I use my computer every day for work.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Technology',
                'cefr_level' => 'A1',
                'meaning' => 'A machine that can store and work with information'
            ],
            [
                'word' => 'phone',
                'pronunciation' => '/foʊn/',
                'definition' => 'A device used to talk to people who are far away',
                'example' => 'Can I use your phone to make a call?',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Technology',
                'cefr_level' => 'A1',
                'meaning' => 'A communication device for speaking with others remotely'
            ],
            [
                'word' => 'internet',
                'pronunciation' => '/ˈɪntərnet/',
                'definition' => 'A global network connecting millions of computers',
                'example' => 'I found this information on the internet.',
                'source' => 'Merriam-Webster',
                'topic' => 'Technology',
                'cefr_level' => 'A1',
                'meaning' => 'Worldwide network for sharing information between computers'
            ],

            // Technology - A2
            [
                'word' => 'software',
                'pronunciation' => '/ˈsɔːftweər/',
                'definition' => 'Programs and operating information used by a computer',
                'example' => 'We need to update the software on all computers.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Technology',
                'cefr_level' => 'A2',
                'meaning' => 'Computer programs and applications'
            ],
            [
                'word' => 'download',
                'pronunciation' => '/ˌdaʊnˈloʊd/',
                'definition' => 'To copy data from one computer system to another',
                'example' => 'You can download the app from the store.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Technology',
                'cefr_level' => 'A2',
                'meaning' => 'Transfer data from the internet to your device'
            ],

            // Technology - B1
            [
                'word' => 'algorithm',
                'pronunciation' => '/ˈælɡərɪðəm/',
                'definition' => 'A process or set of rules to be followed in calculations',
                'example' => 'The search engine uses a complex algorithm.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Technology',
                'cefr_level' => 'B1',
                'meaning' => 'Step-by-step procedure for solving a problem'
            ],

            // Business - A1
            [
                'word' => 'money',
                'pronunciation' => '/ˈmʌni/',
                'definition' => 'Coins or notes used to buy things',
                'example' => 'I need to save money for a new car.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Business',
                'cefr_level' => 'A1',
                'meaning' => 'Currency used for purchasing goods and services'
            ],
            [
                'word' => 'work',
                'pronunciation' => '/wɜːrk/',
                'definition' => 'Activity involving mental or physical effort',
                'example' => 'I work in an office downtown.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Business',
                'cefr_level' => 'A1',
                'meaning' => 'Job or employment activity'
            ],

            // Business - A2
            [
                'word' => 'company',
                'pronunciation' => '/ˈkʌmpəni/',
                'definition' => 'A business organization that sells goods or services',
                'example' => 'She works for a large technology company.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Business',
                'cefr_level' => 'A2',
                'meaning' => 'Commercial business or corporation'
            ],
            [
                'word' => 'customer',
                'pronunciation' => '/ˈkʌstəmər/',
                'definition' => 'A person who buys goods or services',
                'example' => 'The customer complained about the service.',
                'source' => 'Merriam-Webster',
                'topic' => 'Business',
                'cefr_level' => 'A2',
                'meaning' => 'Someone who purchases from a business'
            ],

            // Business - B1
            [
                'word' => 'revenue',
                'pronunciation' => '/ˈrevənuː/',
                'definition' => 'Income generated from normal business operations',
                'example' => 'The company\'s revenue increased by 15% this year.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Business',
                'cefr_level' => 'B1',
                'meaning' => 'Total income earned by a business'
            ],

            // Travel - A1
            [
                'word' => 'hotel',
                'pronunciation' => '/hoʊˈtel/',
                'definition' => 'A place where you pay to stay when traveling',
                'example' => 'We stayed at a nice hotel near the beach.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Travel',
                'cefr_level' => 'A1',
                'meaning' => 'Accommodation for travelers'
            ],
            [
                'word' => 'ticket',
                'pronunciation' => '/ˈtɪkɪt/',
                'definition' => 'A piece of paper that shows you have paid for a journey',
                'example' => 'I bought a train ticket to Paris.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Travel',
                'cefr_level' => 'A1',
                'meaning' => 'Proof of payment for transportation or entry'
            ],

            // Travel - B1
            [
                'word' => 'itinerary',
                'pronunciation' => '/aɪˈtɪnəreri/',
                'definition' => 'A planned route or journey',
                'example' => 'Our travel itinerary includes five cities.',
                'source' => 'Merriam-Webster',
                'topic' => 'Travel',
                'cefr_level' => 'B1',
                'meaning' => 'Detailed plan of a trip'
            ],

            // Food - A1
            [
                'word' => 'bread',
                'pronunciation' => '/bred/',
                'definition' => 'Food made from flour, water, and yeast',
                'example' => 'I eat bread with butter for breakfast.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Food',
                'cefr_level' => 'A1',
                'meaning' => 'Baked staple food'
            ],
            [
                'word' => 'water',
                'pronunciation' => '/ˈwɔːtər/',
                'definition' => 'A clear liquid that has no color, taste, or smell',
                'example' => 'Can I have a glass of water, please?',
                'source' => 'Oxford Dictionary',
                'topic' => 'Food',
                'cefr_level' => 'A1',
                'meaning' => 'Essential liquid for drinking'
            ],

            // Food - B1
            [
                'word' => 'cuisine',
                'pronunciation' => '/kwɪˈziːn/',
                'definition' => 'A style of cooking',
                'example' => 'I love Italian cuisine, especially pasta.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Food',
                'cefr_level' => 'B1',
                'meaning' => 'Type of food or cooking style from a particular country'
            ],

            // Health - A1
            [
                'word' => 'doctor',
                'pronunciation' => '/ˈdɑːktər/',
                'definition' => 'A person who treats people who are ill',
                'example' => 'I need to see a doctor about my cough.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Health',
                'cefr_level' => 'A1',
                'meaning' => 'Medical professional who treats patients'
            ],
            [
                'word' => 'medicine',
                'pronunciation' => '/ˈmedɪsn/',
                'definition' => 'A substance used to treat illness',
                'example' => 'Take this medicine three times a day.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Health',
                'cefr_level' => 'A1',
                'meaning' => 'Drug or treatment for illness'
            ],

            // Health - B2
            [
                'word' => 'diagnosis',
                'pronunciation' => '/ˌdaɪəɡˈnoʊsɪs/',
                'definition' => 'The identification of an illness by examining symptoms',
                'example' => 'The doctor gave a diagnosis of flu.',
                'source' => 'Merriam-Webster',
                'topic' => 'Health',
                'cefr_level' => 'B2',
                'meaning' => 'Medical determination of a disease'
            ],

            // Education - A1
            [
                'word' => 'student',
                'pronunciation' => '/ˈstuːdnt/',
                'definition' => 'A person who is learning at a school or university',
                'example' => 'There are 30 students in my class.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Education',
                'cefr_level' => 'A1',
                'meaning' => 'Person studying at an educational institution'
            ],
            [
                'word' => 'teacher',
                'pronunciation' => '/ˈtiːtʃər/',
                'definition' => 'A person who teaches, especially in a school',
                'example' => 'My teacher explains things very clearly.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Education',
                'cefr_level' => 'A1',
                'meaning' => 'Person who instructs students'
            ],

            // Education - B2
            [
                'word' => 'curriculum',
                'pronunciation' => '/kəˈrɪkjələm/',
                'definition' => 'The subjects studied in a school or course',
                'example' => 'The new curriculum includes coding classes.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Education',
                'cefr_level' => 'B2',
                'meaning' => 'Course of study at an educational institution'
            ],

            // Sports - A1
            [
                'word' => 'ball',
                'pronunciation' => '/bɔːl/',
                'definition' => 'A round object used in games and sports',
                'example' => 'Kick the ball to me!',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Sports',
                'cefr_level' => 'A1',
                'meaning' => 'Round object used in sports'
            ],
            [
                'word' => 'game',
                'pronunciation' => '/ɡeɪm/',
                'definition' => 'A form of play or sport with rules',
                'example' => 'We won the game 3-1.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Sports',
                'cefr_level' => 'A1',
                'meaning' => 'Competitive activity with rules'
            ],

            // Sports - B1
            [
                'word' => 'tournament',
                'pronunciation' => '/ˈtʊrnəmənt/',
                'definition' => 'A competition with several rounds',
                'example' => 'She entered the tennis tournament.',
                'source' => 'Merriam-Webster',
                'topic' => 'Sports',
                'cefr_level' => 'B1',
                'meaning' => 'Series of competitive matches'
            ],

            // Science - A2
            [
                'word' => 'experiment',
                'pronunciation' => '/ɪkˈsperɪmənt/',
                'definition' => 'A scientific test to discover something',
                'example' => 'We did an experiment in chemistry class.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Science',
                'cefr_level' => 'A2',
                'meaning' => 'Scientific procedure to test a hypothesis'
            ],

            // Science - C1
            [
                'word' => 'hypothesis',
                'pronunciation' => '/haɪˈpɑːθəsɪs/',
                'definition' => 'A proposed explanation that needs testing',
                'example' => 'The scientist\'s hypothesis was proven correct.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Science',
                'cefr_level' => 'C1',
                'meaning' => 'Tentative explanation requiring verification'
            ],

            // Nature - A1
            [
                'word' => 'tree',
                'pronunciation' => '/triː/',
                'definition' => 'A tall plant with a trunk and branches',
                'example' => 'There are many trees in the park.',
                'source' => 'Cambridge Dictionary',
                'topic' => 'Nature',
                'cefr_level' => 'A1',
                'meaning' => 'Large woody plant'
            ],
            [
                'word' => 'flower',
                'pronunciation' => '/ˈflaʊər/',
                'definition' => 'The colored part of a plant',
                'example' => 'I bought some flowers for my mother.',
                'source' => 'Oxford Dictionary',
                'topic' => 'Nature',
                'cefr_level' => 'A1',
                'meaning' => 'Colorful blooming part of a plant'
            ],

            // Nature - B2
            [
                'word' => 'ecosystem',
                'pronunciation' => '/ˈiːkoʊsɪstəm/',
                'definition' => 'A biological community of interacting organisms',
                'example' => 'The forest ecosystem supports diverse wildlife.',
                'source' => 'Merriam-Webster',
                'topic' => 'Nature',
                'cefr_level' => 'B2',
                'meaning' => 'Community of living organisms and their environment'
            ],
        ];
    }
}