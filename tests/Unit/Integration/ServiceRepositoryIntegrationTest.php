<?php

namespace Tests\Unit\Integration;

use App\Models\DailyWordHistory;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use App\Repositories\Eloquent\EloquentSubscriptionRepository;
use App\Repositories\Eloquent\EloquentUserWordRepository;
use App\Repositories\Eloquent\EloquentWordRepository;
use App\Services\DailyWordService;
use App\Services\SubscriptionService;
use App\Services\UserVocabularyService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ServiceRepositoryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function daily_word_service_integrates_with_word_repository()
    {
        // Arrange
        $wordRepository = new EloquentWordRepository();
        $dailyWordService = new DailyWordService($wordRepository);
        
        Word::factory()->count(10)->create();

        // Act
        $word = $dailyWordService->getTodayWord();

        // Assert
        $this->assertInstanceOf(Word::class, $word);
        $this->assertDatabaseHas('daily_word_history', [
            'word_id' => $word->id,
            'date' => Carbon::now()->toDateString()
        ]);
    }

    /** @test */
    public function daily_word_service_returns_different_word_for_user_with_existing_word()
    {
        // Arrange
        $wordRepository = new EloquentWordRepository();
        $dailyWordService = new DailyWordService($wordRepository);
        
        $user = User::factory()->create();
        $dailyWord = Word::factory()->create(['word' => 'daily']);
        $otherWord = Word::factory()->create(['word' => 'other']);
        
        // Create today's word
        DailyWordHistory::create([
            'word_id' => $dailyWord->id,
            'date' => Carbon::now()->toDateString()
        ]);
        
        // User already has the daily word
        UserWord::create([
            'user_id' => $user->id,
            'word_id' => $dailyWord->id,
            'status' => 'saved'
        ]);

        // Act
        $result = $dailyWordService->getTodayWord($user->id);

        // Assert
        $this->assertInstanceOf(Word::class, $result);
        $this->assertNotEquals($dailyWord->id, $result->id);
        $this->assertEquals($otherWord->id, $result->id);
    }

    /** @test */
    public function subscription_service_integrates_with_subscription_repository()
    {
        // Arrange
        Mail::fake();
        $subscriptionRepository = new EloquentSubscriptionRepository();
        $subscriptionService = new SubscriptionService($subscriptionRepository);
        
        $email = 'test@example.com';

        // Act
        $subscription = $subscriptionService->subscribe($email);

        // Assert
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($email, $subscription->email);
        $this->assertNull($subscription->confirmed_at);
        
        $this->assertDatabaseHas('subscriptions', [
            'email' => $email,
            'confirmed_at' => null
        ]);
        
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    /** @test */
    public function subscription_service_handles_existing_subscription()
    {
        // Arrange
        Mail::fake();
        $subscriptionRepository = new EloquentSubscriptionRepository();
        $subscriptionService = new SubscriptionService($subscriptionRepository);
        
        $email = 'test@example.com';
        $existingSubscription = Subscription::factory()->confirmed()->create([
            'email' => $email
        ]);

        // Act
        $result = $subscriptionService->subscribe($email);

        // Assert
        $this->assertEquals($existingSubscription->id, $result->id);
        Mail::assertNothingSent();
    }

    /** @test */
    public function user_vocabulary_service_integrates_with_user_word_repository()
    {
        // Arrange
        $userWordRepository = new EloquentUserWordRepository();
        $userVocabularyService = new UserVocabularyService($userWordRepository);
        
        $user = User::factory()->create();
        $word = Word::factory()->create();

        // Act
        $userWord = $userVocabularyService->addWord($user->id, $word->id);

        // Assert
        $this->assertInstanceOf(UserWord::class, $userWord);
        $this->assertEquals($user->id, $userWord->user_id);
        $this->assertEquals($word->id, $userWord->word_id);
        $this->assertEquals('saved', $userWord->status);
        
        $this->assertDatabaseHas('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved'
        ]);
    }

    /** @test */
    public function user_vocabulary_service_manages_word_lifecycle()
    {
        // Arrange
        $userWordRepository = new EloquentUserWordRepository();
        $userVocabularyService = new UserVocabularyService($userWordRepository);
        
        $user = User::factory()->create();
        $word = Word::factory()->create();

        // Act & Assert - Add word
        $userWord = $userVocabularyService->addWord($user->id, $word->id);
        $this->assertEquals('saved', $userWord->status);

        // Act & Assert - Mark as learned
        $result = $userVocabularyService->markLearned($user->id, $word->id);
        $this->assertTrue($result);
        
        $this->assertDatabaseHas('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'learned'
        ]);

        // Act & Assert - Get user words
        $userWords = $userVocabularyService->getUserWords($user->id);
        $this->assertCount(1, $userWords);
        $this->assertEquals('learned', $userWords->first()->status);

        // Act & Assert - Remove word
        $removed = $userVocabularyService->removeWord($user->id, $word->id);
        $this->assertTrue($removed);
        
        $this->assertDatabaseMissing('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id
        ]);
    }

    /** @test */
    public function repositories_handle_edge_cases_correctly()
    {
        // Arrange
        $wordRepository = new EloquentWordRepository();
        $userWordRepository = new EloquentUserWordRepository();
        $subscriptionRepository = new EloquentSubscriptionRepository();
        
        $user = User::factory()->create();

        // Test empty states
        $this->assertNull($wordRepository->getRandomWord());
        $this->assertNull($wordRepository->findById(999));
        $this->assertTrue($userWordRepository->getUserWords($user->id)->isEmpty());
        $this->assertNull($subscriptionRepository->findByEmail('nonexistent@example.com'));
        
        // Test with data
        $words = Word::factory()->count(5)->create();
        $randomWord = $wordRepository->getRandomWord();
        $this->assertInstanceOf(Word::class, $randomWord);
        $this->assertContains($randomWord->id, $words->pluck('id'));
        
        // Test search functionality
        Word::factory()->create(['word' => 'exceptional', 'definition' => 'outstanding']);
        $searchResults = $wordRepository->search('except');
        $this->assertCount(1, $searchResults);
        $this->assertEquals('exceptional', $searchResults->first()->word);
    }
}