<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['word' => 'abandon', 'definition' => 'To leave someone or something behind.', 'example' => 'He abandoned the car in the middle of the road.'],
            ['word' => 'benevolent', 'definition' => 'Kind and generous.', 'example' => 'She was a benevolent leader.'],
            ['word' => 'candid', 'definition' => 'Truthful and straightforward.', 'example' => 'He gave a candid interview.'],
            ['word' => 'diligent', 'definition' => 'Hard-working and careful.', 'example' => 'She is a diligent student who studies every night.'],
            ['word' => 'elaborate', 'definition' => 'Involving many careful details.', 'example' => 'They made an elaborate plan for the wedding.'],
            ['word' => 'fascinate', 'definition' => 'To attract someone’s attention strongly.', 'example' => 'Space exploration fascinates me.'],
            ['word' => 'generous', 'definition' => 'Willing to give and share freely.', 'example' => 'He is always generous with his time.'],
            ['word' => 'humble', 'definition' => 'Not proud; modest.', 'example' => 'Despite his success, he remained humble.'],
            ['word' => 'illustrate', 'definition' => 'To explain or make something clear by using examples.', 'example' => 'The teacher illustrated the concept with a diagram.'],
            ['word' => 'justify', 'definition' => 'To show or prove that something is right or reasonable.', 'example' => 'He tried to justify his late arrival.'],

            ['word' => 'keen', 'definition' => 'Very interested, eager, or enthusiastic.', 'example' => 'She is a keen observer of human behavior.'],
            ['word' => 'lament', 'definition' => 'To express sorrow or regret.', 'example' => 'They lamented the loss of their old traditions.'],
            ['word' => 'meticulous', 'definition' => 'Extremely careful and precise.', 'example' => 'He kept meticulous records of every transaction.'],
            ['word' => 'notion', 'definition' => 'An idea, belief, or opinion.', 'example' => 'She had a romantic notion about living abroad.'],
            ['word' => 'obscure', 'definition' => 'Not well known or difficult to understand.', 'example' => 'The meaning of the poem is obscure.'],
            ['word' => 'perceive', 'definition' => 'To become aware of something through the senses.', 'example' => 'I perceived a slight change in his tone.'],
            ['word' => 'quaint', 'definition' => 'Attractively unusual or old-fashioned.', 'example' => 'They stayed in a quaint little cottage.'],
            ['word' => 'reluctant', 'definition' => 'Unwilling or hesitant.', 'example' => 'She was reluctant to talk about the past.'],
            ['word' => 'sincere', 'definition' => 'Free from pretense; genuine.', 'example' => 'He offered a sincere apology.'],
            ['word' => 'thrive', 'definition' => 'To grow or develop successfully.', 'example' => 'The business continues to thrive.'],

            ['word' => 'ultimate', 'definition' => 'Being the best or most extreme example.', 'example' => 'Winning the championship was their ultimate goal.'],
            ['word' => 'vague', 'definition' => 'Not clearly expressed or understood.', 'example' => 'His answer was too vague to be useful.'],
            ['word' => 'wander', 'definition' => 'To move around without purpose or direction.', 'example' => 'They wandered through the park.'],
            ['word' => 'yearn', 'definition' => 'To have an intense longing for something.', 'example' => 'She yearned for a chance to travel.'],
            ['word' => 'zealous', 'definition' => 'Showing great enthusiasm or passion.', 'example' => 'He is a zealous supporter of environmental causes.'],
            ['word' => 'allocate', 'definition' => 'To distribute resources or duties for a purpose.', 'example' => 'Funds were allocated to improve education.'],
            ['word' => 'boast', 'definition' => 'To talk with pride about achievements.', 'example' => 'He likes to boast about his car.'],
            ['word' => 'coherent', 'definition' => 'Logical and consistent.', 'example' => 'She gave a coherent explanation.'],
            ['word' => 'devote', 'definition' => 'To give time or effort to something.', 'example' => 'He devoted his life to science.'],
            ['word' => 'emerge', 'definition' => 'To become visible or known.', 'example' => 'A new leader emerged from the crowd.'],

            ['word' => 'forbid', 'definition' => 'To order someone not to do something.', 'example' => 'Smoking is forbidden here.'],
            ['word' => 'gratitude', 'definition' => 'The quality of being thankful.', 'example' => 'She expressed her gratitude for their help.'],
            ['word' => 'harsh', 'definition' => 'Unpleasantly rough or severe.', 'example' => 'The desert climate is very harsh.'],
            ['word' => 'imply', 'definition' => 'To suggest something without saying it directly.', 'example' => 'Are you implying that I lied?'],
            ['word' => 'jolly', 'definition' => 'Happy and cheerful.', 'example' => 'He was in a jolly mood.'],
            ['word' => 'knack', 'definition' => 'A special skill or ability.', 'example' => 'She has a knack for solving puzzles.'],
            ['word' => 'linger', 'definition' => 'To stay longer than expected.', 'example' => 'He lingered at the café after closing time.'],
            ['word' => 'manifest', 'definition' => 'To show or demonstrate clearly.', 'example' => 'His fear was manifest in his eyes.'],
            ['word' => 'negligent', 'definition' => 'Failing to take proper care.', 'example' => 'The driver was found negligent.'],
            ['word' => 'oblige', 'definition' => 'To do something because it is necessary or polite.', 'example' => 'She was obliged to help her friend.'],

            ['word' => 'paradox', 'definition' => 'A statement that seems contradictory but may be true.', 'example' => 'It’s a paradox that exercise can make you tired but also more energetic.'],
            ['word' => 'quench', 'definition' => 'To satisfy thirst or desire.', 'example' => 'He drank water to quench his thirst.'],
            ['word' => 'retain', 'definition' => 'To keep or continue to have something.', 'example' => 'The company retains most of its employees.'],
            ['word' => 'sustain', 'definition' => 'To support or maintain.', 'example' => 'They worked to sustain economic growth.'],
            ['word' => 'tangible', 'definition' => 'Something real and touchable.', 'example' => 'The results are tangible and measurable.'],
            ['word' => 'urge', 'definition' => 'To strongly encourage someone to do something.', 'example' => 'He urged me to apply for the job.'],
            ['word' => 'vivid', 'definition' => 'Producing strong, clear images in the mind.', 'example' => 'She gave a vivid description of her journey.'],
            ['word' => 'witty', 'definition' => 'Clever and amusing.', 'example' => 'His witty jokes made everyone laugh.'],
            ['word' => 'yield', 'definition' => 'To produce or provide something.', 'example' => 'The farm yields good crops.'],
            ['word' => 'zenith', 'definition' => 'The highest point or peak.', 'example' => 'At the zenith of his career, he retired.'],

            ['word' => 'abrupt', 'definition' => 'Sudden and unexpected.', 'example' => 'The meeting ended abruptly.'],
            ['word' => 'brisk', 'definition' => 'Quick and active; energetic.', 'example' => 'They took a brisk walk.'],
            ['word' => 'convey', 'definition' => 'To communicate or express.', 'example' => 'Words can’t convey how happy I am.'],
            ['word' => 'deter', 'definition' => 'To discourage someone from doing something.', 'example' => 'High fines deter people from speeding.'],
            ['word' => 'endure', 'definition' => 'To suffer patiently; to tolerate.', 'example' => 'They had to endure the storm all night.'],
            ['word' => 'flourish', 'definition' => 'To grow or develop successfully.', 'example' => 'Her garden flourished in spring.'],
            ['word' => 'gleam', 'definition' => 'To shine softly.', 'example' => 'Her eyes gleamed with excitement.'],
            ['word' => 'hinder', 'definition' => 'To make it difficult for something to happen.', 'example' => 'Bad weather hindered the rescue effort.'],
            ['word' => 'immerse', 'definition' => 'To involve deeply in an activity or experience.', 'example' => 'She immersed herself in studying.'],
            ['word' => 'juggle', 'definition' => 'To manage several things at the same time.', 'example' => 'He juggles work and family duties.'],

            ['word' => 'kindle', 'definition' => 'To start a fire or inspire emotion.', 'example' => 'The teacher kindled a love of learning in her students.'],
            ['word' => 'lamentable', 'definition' => 'Deserving regret or pity.', 'example' => 'It was a lamentable mistake.'],
            ['word' => 'mourn', 'definition' => 'To feel or show deep sorrow.', 'example' => 'They mourned the loss of their friend.'],
            ['word' => 'nurture', 'definition' => 'To care for and encourage growth.', 'example' => 'Parents should nurture their children’s talents.'],
            ['word' => 'overwhelm', 'definition' => 'To overpower emotionally or physically.', 'example' => 'She was overwhelmed by gratitude.'],
            ['word' => 'ponder', 'definition' => 'To think deeply about something.', 'example' => 'He pondered the question for hours.'],
            ['word' => 'quarrel', 'definition' => 'A heated argument or disagreement.', 'example' => 'They had a quarrel about money.'],
            ['word' => 'resemble', 'definition' => 'To look like or be similar to.', 'example' => 'He resembles his father.'],
            ['word' => 'soothe', 'definition' => 'To calm or relieve.', 'example' => 'Music can soothe the soul.'],
            ['word' => 'tremble', 'definition' => 'To shake involuntarily.', 'example' => 'He trembled with fear.'],

            ['word' => 'utter', 'definition' => 'To speak or say something aloud.', 'example' => 'She uttered a small cry of surprise.'],
            ['word' => 'vanish', 'definition' => 'To disappear suddenly.', 'example' => 'The magician made the rabbit vanish.'],
            ['word' => 'waver', 'definition' => 'To be uncertain or indecisive.', 'example' => 'She wavered between hope and fear.'],
            ['word' => 'yawn', 'definition' => 'To open the mouth wide, usually when tired.', 'example' => 'He yawned loudly during the lecture.'],
            ['word' => 'zeal', 'definition' => 'Great energy or enthusiasm.', 'example' => 'She worked with zeal to finish the project.'],
            ['word' => 'amend', 'definition' => 'To change or improve something.', 'example' => 'They amended the contract to include new terms.'],
            ['word' => 'bewilder', 'definition' => 'To confuse completely.', 'example' => 'The complex directions bewildered him.'],
            ['word' => 'collaborate', 'definition' => 'To work together on a task.', 'example' => 'Two artists collaborated on the mural.'],
            ['word' => 'diminish', 'definition' => 'To make smaller or less important.', 'example' => 'Time will not diminish his achievements.'],
            ['word' => 'enhance', 'definition' => 'To improve the quality or value of something.', 'example' => 'New features enhance the product’s appeal.'],
        ];

        foreach ($data as $item) {
            Word::create($item);
        }
    }
}
