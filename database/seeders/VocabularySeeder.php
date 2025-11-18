<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vocabulary = [
            // A1 Level - Beginner
            [
                'word' => 'book',
                'pronunciation' => '/bʊk/',
                'definition' => 'A written or printed work consisting of pages glued or sewn together along one side and bound in covers.',
                'example' => 'She opened the book and began to read the first chapter.',
                'source' => 'oxford',
                'topic' => 'Education',
                'cefr_level' => 'A1',
                'meaning' => 'An object containing written or printed pages that you can read for information or entertainment.',
            ],
            [
                'word' => 'happy',
                'pronunciation' => '/ˈhæpi/',
                'definition' => 'Feeling or showing pleasure or contentment.',
                'example' => 'The children were happy playing in the garden.',
                'source' => 'cambridge',
                'topic' => 'Emotions',
                'cefr_level' => 'A1',
                'meaning' => 'A positive emotion characterized by joy, satisfaction, contentment, and fulfillment.',
            ],
            [
                'word' => 'house',
                'pronunciation' => '/haʊs/',
                'definition' => 'A building for human habitation, especially one that consists of a ground floor and usually one or more upper storeys.',
                'example' => 'They bought a beautiful house near the lake.',
                'source' => 'oxford',
                'topic' => 'Family',
                'cefr_level' => 'A1',
                'meaning' => 'A structure built for people to live in, typically consisting of rooms, walls, and a roof.',
            ],
            [
                'word' => 'water',
                'pronunciation' => '/ˈwɔːtər/',
                'definition' => 'A colorless, transparent, odorless liquid that forms the seas, lakes, rivers, and rain.',
                'example' => 'Please drink plenty of water to stay hydrated.',
                'source' => 'merriam-webster',
                'topic' => 'Nature',
                'cefr_level' => 'A1',
                'meaning' => 'The clear liquid that has no color or taste when it is pure and that falls from clouds as rain.',
            ],
            [
                'word' => 'friend',
                'pronunciation' => '/frend/',
                'definition' => 'A person with whom one has a bond of mutual affection.',
                'example' => 'She has been my best friend since childhood.',
                'source' => 'cambridge',
                'topic' => 'Family',
                'cefr_level' => 'A1',
                'meaning' => 'Someone you know well and like spending time with, but who is not related to you.',
            ],

            // A2 Level - Elementary
            [
                'word' => 'environment',
                'pronunciation' => '/ɪnˈvaɪrənmənt/',
                'definition' => 'The surroundings or conditions in which a person, animal, or plant lives or operates.',
                'example' => 'We must protect the environment for future generations.',
                'source' => 'oxford',
                'topic' => 'Nature',
                'cefr_level' => 'A2',
                'meaning' => 'The natural world around us, including the air, water, and land in which people, animals, and plants live.',
            ],
            [
                'word' => 'restaurant',
                'pronunciation' => '/ˈrestərɑːnt/',
                'definition' => 'A place where people pay to sit and eat meals that are cooked and served on the premises.',
                'example' => 'We had dinner at a lovely Italian restaurant downtown.',
                'source' => 'cambridge',
                'topic' => 'Food',
                'cefr_level' => 'A2',
                'meaning' => 'A business where people can buy and eat prepared food and drinks.',
            ],
            [
                'word' => 'computer',
                'pronunciation' => '/kəmˈpjuːtər/',
                'definition' => 'An electronic device for storing and processing data according to instructions given to it in a variable program.',
                'example' => 'I use my computer for both work and entertainment.',
                'source' => 'merriam-webster',
                'topic' => 'Technology',
                'cefr_level' => 'A2',
                'meaning' => 'An electronic machine that can store, organize, and find information, do calculations, and control other machines.',
            ],
            [
                'word' => 'vacation',
                'pronunciation' => '/vəˈkeɪʃən/',
                'definition' => 'An extended period of leisure and recreation, especially one spent away from home or in traveling.',
                'example' => 'We are planning a vacation to Europe next summer.',
                'source' => 'oxford',
                'topic' => 'Travel',
                'cefr_level' => 'A2',
                'meaning' => 'A period of time when you are not working or studying and can relax or travel.',
            ],
            [
                'word' => 'exercise',
                'pronunciation' => '/ˈeksərsaɪz/',
                'definition' => 'Activity requiring physical effort, carried out to sustain or improve health and fitness.',
                'example' => 'Regular exercise is important for maintaining good health.',
                'source' => 'cambridge',
                'topic' => 'Health',
                'cefr_level' => 'A2',
                'meaning' => 'Physical activity that you do to make your body strong and healthy.',
            ],

            // B1 Level - Intermediate
            [
                'word' => 'entrepreneur',
                'pronunciation' => '/ˌɑːntrəprəˈnɜːr/',
                'definition' => 'A person who organizes and operates a business or businesses, taking on greater than normal financial risks in order to do so.',
                'example' => 'The young entrepreneur started her own tech company at age 25.',
                'source' => 'oxford',
                'topic' => 'Business',
                'cefr_level' => 'B1',
                'meaning' => 'Someone who starts their own business, especially when this involves seeing a new opportunity and taking risks.',
            ],
            [
                'word' => 'sustainable',
                'pronunciation' => '/səˈsteɪnəbəl/',
                'definition' => 'Able to be maintained at a certain rate or level; avoiding the depletion of natural resources.',
                'example' => 'The company is committed to sustainable business practices.',
                'source' => 'cambridge',
                'topic' => 'Environment',
                'cefr_level' => 'B1',
                'meaning' => 'Causing little or no damage to the environment and therefore able to continue for a long time.',
            ],
            [
                'word' => 'creativity',
                'pronunciation' => '/ˌkriːeɪˈtɪvəti/',
                'definition' => 'The use of imagination or original ideas to create something; inventiveness.',
                'example' => 'The project requires both technical skills and creativity.',
                'source' => 'merriam-webster',
                'topic' => 'Education',
                'cefr_level' => 'B1',
                'meaning' => 'The ability to produce original and unusual ideas, or to make something new or imaginative.',
            ],
            [
                'word' => 'championship',
                'pronunciation' => '/ˈtʃæmpiənʃɪp/',
                'definition' => 'A competition for the position of champion in a sport or game.',
                'example' => 'The tennis championship will be held in Wimbledon this year.',
                'source' => 'oxford',
                'topic' => 'Sports',
                'cefr_level' => 'B1',
                'meaning' => 'A competition to find the best player or team in a particular sport.',
            ],
            [
                'word' => 'archaeology',
                'pronunciation' => '/ˌɑːrkiˈɑːlədʒi/',
                'definition' => 'The study of human history and prehistory through the excavation of sites and the analysis of artifacts.',
                'example' => 'She pursued a degree in archaeology to study ancient civilizations.',
                'source' => 'cambridge',
                'topic' => 'Science',
                'cefr_level' => 'B1',
                'meaning' => 'The study of the buildings, tools, and other objects that belonged to people who lived in the past.',
            ],

            // B2 Level - Upper Intermediate
            [
                'word' => 'metamorphosis',
                'pronunciation' => '/ˌmetəˈmɔːrfəsɪs/',
                'definition' => 'A change of the form or nature of a thing or person into a completely different one.',
                'example' => 'The caterpillar undergoes metamorphosis to become a butterfly.',
                'source' => 'oxford',
                'topic' => 'Science',
                'cefr_level' => 'B2',
                'meaning' => 'A complete change in appearance, character, circumstances, etc.',
            ],
            [
                'word' => 'procrastination',
                'pronunciation' => '/prəˌkræstɪˈneɪʃən/',
                'definition' => 'The action of delaying or postponing something.',
                'example' => 'His procrastination led to missed deadlines and increased stress.',
                'source' => 'merriam-webster',
                'topic' => 'Work',
                'cefr_level' => 'B2',
                'meaning' => 'The act of delaying something that must be done, often because it is unpleasant or boring.',
            ],
            [
                'word' => 'sophisticated',
                'pronunciation' => '/səˈfɪstɪkeɪtɪd/',
                'definition' => 'Having great knowledge or experience of the world and knowing about fashion, culture, and other things that people think are socially important.',
                'example' => 'The restaurant has a sophisticated atmosphere and excellent cuisine.',
                'source' => 'cambridge',
                'topic' => 'Culture',
                'cefr_level' => 'B2',
                'meaning' => 'Intelligent or made in a complicated way and therefore able to do complicated tasks.',
            ],
            [
                'word' => 'perseverance',
                'pronunciation' => '/ˌpɜːrsəˈvɪrəns/',
                'definition' => 'Persistence in doing something despite difficulty or delay in achieving success.',
                'example' => 'Her perseverance in learning the language finally paid off.',
                'source' => 'oxford',
                'topic' => 'Education',
                'cefr_level' => 'B2',
                'meaning' => 'The quality of continuing to try to achieve a particular aim despite difficulties.',
            ],
            [
                'word' => 'biodiversity',
                'pronunciation' => '/ˌbaɪoʊdaɪˈvɜːrsəti/',
                'definition' => 'The variety of life in the world or in a particular habitat or ecosystem.',
                'example' => 'The rainforest is known for its incredible biodiversity.',
                'source' => 'cambridge',
                'topic' => 'Nature',
                'cefr_level' => 'B2',
                'meaning' => 'The number and variety of plant and animal species that exist in a particular environmental area.',
            ],

            // C1 Level - Advanced
            [
                'word' => 'serendipity',
                'pronunciation' => '/ˌserənˈdɪpəti/',
                'definition' => 'The occurrence and development of events by chance in a happy or beneficial way.',
                'example' => 'It was pure serendipity that led to their groundbreaking discovery.',
                'source' => 'oxford',
                'topic' => 'Science',
                'cefr_level' => 'C1',
                'meaning' => 'The fact of finding interesting or valuable things by chance.',
            ],
            [
                'word' => 'juxtaposition',
                'pronunciation' => '/ˌdʒʌkstəpəˈzɪʃən/',
                'definition' => 'The fact of two things being seen or placed close together with contrasting effect.',
                'example' => 'The juxtaposition of old and new architecture creates visual interest.',
                'source' => 'cambridge',
                'topic' => 'Culture',
                'cefr_level' => 'C1',
                'meaning' => 'The act of putting two things that are very different next to each other.',
            ],
            [
                'word' => 'ubiquitous',
                'pronunciation' => '/juːˈbɪkwɪtəs/',
                'definition' => 'Present, appearing, or found everywhere.',
                'example' => 'Smartphones have become ubiquitous in modern society.',
                'source' => 'merriam-webster',
                'topic' => 'Technology',
                'cefr_level' => 'C1',
                'meaning' => 'Seeming to be everywhere at the same time; very common.',
            ],
            [
                'word' => 'paradigm',
                'pronunciation' => '/ˈpærədaɪm/',
                'definition' => 'A typical example or pattern of something; a model.',
                'example' => 'The new research challenges the existing paradigm in neuroscience.',
                'source' => 'oxford',
                'topic' => 'Science',
                'cefr_level' => 'C1',
                'meaning' => 'A model of something, or a very clear and typical example of something.',
            ],
            [
                'word' => 'transcendental',
                'pronunciation' => '/ˌtrænsenˈdentəl/',
                'definition' => 'Relating to a spiritual or non-physical realm; surpassing the ordinary; exceptional.',
                'example' => 'The meditation practice led to a transcendental experience.',
                'source' => 'cambridge',
                'topic' => 'Emotions',
                'cefr_level' => 'C1',
                'meaning' => 'Going beyond ordinary limits; surpassing; exceeding; extraordinary.',
            ],

            // C2 Level - Proficient
            [
                'word' => 'perspicacious',
                'pronunciation' => '/ˌpɜːrspɪˈkeɪʃəs/',
                'definition' => 'Having a ready insight into and understanding of things; acutely perceptive.',
                'example' => 'Her perspicacious analysis of the market trends impressed the board.',
                'source' => 'oxford',
                'topic' => 'Business',
                'cefr_level' => 'C2',
                'meaning' => 'Quick to notice and understand things that are not obvious.',
            ],
            [
                'word' => 'ephemeral',
                'pronunciation' => '/ɪˈfemərəl/',
                'definition' => 'Lasting for a very short time.',
                'example' => 'The beauty of cherry blossoms is ephemeral, lasting only a few weeks.',
                'source' => 'cambridge',
                'topic' => 'Nature',
                'cefr_level' => 'C2',
                'meaning' => 'Lasting for only a short time; transitory.',
            ],
            [
                'word' => 'quintessential',
                'pronunciation' => '/ˌkwɪntɪˈsenʃəl/',
                'definition' => 'Representing the most perfect or typical example of a quality or class.',
                'example' => 'He is the quintessential gentleman, always polite and considerate.',
                'source' => 'merriam-webster',
                'topic' => 'Culture',
                'cefr_level' => 'C2',
                'meaning' => 'Being a perfect example of a particular quality.',
            ],
            [
                'word' => 'obsequious',
                'pronunciation' => '/əbˈsiːkwiəs/',
                'definition' => 'Obedient or attentive to an excessive or servile degree.',
                'example' => 'His obsequious behavior toward the boss made his colleagues uncomfortable.',
                'source' => 'oxford',
                'topic' => 'Work',
                'cefr_level' => 'C2',
                'meaning' => 'Too eager to help or obey someone important.',
            ],
            [
                'word' => 'magnanimous',
                'pronunciation' => '/mægˈnænɪməs/',
                'definition' => 'Very kind and generous toward an opponent or less powerful person.',
                'example' => 'The champion was magnanimous in victory, praising his opponent.',
                'source' => 'cambridge',
                'topic' => 'Sports',
                'cefr_level' => 'C2',
                'meaning' => 'Generous in forgiving an insult or injury; free from petty resentfulness.',
            ],
        ];

        // Clear existing placeholder data
        Word::whereRaw("word LIKE 'word_%' OR word LIKE 'placeholder_%'")->delete();

        // Insert the real vocabulary
        foreach ($vocabulary as $wordData) {
            Word::updateOrCreate(
                ['word' => $wordData['word']],
                $wordData
            );
        }
    }
}