<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SavedSession;
use App\Http\Requests\StoreSavedSessionRequest;
use App\Http\Requests\UpdateSavedSessionRequest;
use App\Http\Requests\ReviewSavedSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for managing saved study sessions.
 * 
 * Handles CRUD operations for saved sessions and provides
 * functionality to review saved sessions.
 */
class SavedSessionController extends Controller
{
    /**
     * Display a listing of the user's saved sessions.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $sessions = SavedSession::forUser($user->id)
            ->recent()
            ->with(['items' => function($query) {
                $query->orderBy('position')->with('word');
            }])
            ->paginate(12);

        // Add flashcard count to each session
        $sessions->getCollection()->transform(function ($session) {
            $session->flashcard_count = $session->items->count();
            return $session;
        });

        if ($request->expectsJson()) {
            return response()->json($sessions);
        }

        return Inertia::render('SavedSessions/Index', [
            'sessions' => $sessions
        ]);
    }

    /**
     * Store a newly created saved session in storage.
     *
     * @param StoreSavedSessionRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(StoreSavedSessionRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate unique slug
            $slug = SavedSession::generateUniqueSlug($validated['name'], $user->id);

            // Create saved session
            $savedSession = SavedSession::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'slug' => $slug,
                'topic' => $validated['topic'] ?? null,
            ]);

            // Create session items
            $flashcardIds = $validated['flashcard_ids'];
            $items = [];
            
            foreach ($flashcardIds as $position => $flashcardId) {
                $items[] = [
                    'saved_session_id' => $savedSession->id,
                    'flashcard_id' => $flashcardId,
                    'position' => $position + 1, // 1-based indexing
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $savedSession->items()->createMany($items);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session đã được lưu thành công!',
                    'session' => $savedSession->load('items')
                ], 201);
            }

            return redirect()->route('saved-sessions.show', $savedSession->slug)
                ->with('success', 'Session đã được lưu thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra khi lưu session.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra khi lưu session.');
        }
    }

    /**
     * Display the specified saved session.
     *
     * @param string $slug
     * @param Request $request
     * @return JsonResponse|Response|RedirectResponse
     */
    public function show(string $slug, Request $request)
    {
        // Force check authentication
        if (!Auth::check()) {
            Log::warning('SavedSessionController::show - User not authenticated, redirecting to login');
            return redirect()->route('login')->with('error', 'Please log in to access saved sessions.');
        }
        
        $user = Auth::user();
        
        // Debug authentication state
        Log::info('SavedSessionController::show - Authentication debug', [
            'is_authenticated' => true,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'slug' => $slug,
            'session_id' => session()->getId(),
            'request_url' => $request->fullUrl(),
        ]);
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $slug)
            ->with(['items' => function($query) {
                $query->orderBy('position')->with('word');
            }])
            ->firstOrFail();

        if ($request->expectsJson()) {
            return response()->json($session);
        }

        return Inertia::render('SavedSessions/Show', [
            'session' => $session
        ]);
    }

    /**
     * Update the specified saved session in storage.
     *
     * @param UpdateSavedSessionRequest $request
     * @param string $slug
     * @return JsonResponse|RedirectResponse
     */
    public function update(UpdateSavedSessionRequest $request, string $slug)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $session = SavedSession::forUser($user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if user owns this session (additional security)
        if ($session->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Update basic info
            if (isset($validated['name'])) {
                // Generate new slug if name changed
                if ($session->name !== $validated['name']) {
                    $newSlug = SavedSession::generateUniqueSlug($validated['name'], $user->id);
                    $session->slug = $newSlug;
                }
                $session->name = $validated['name'];
            }

            if (array_key_exists('topic', $validated)) {
                $session->topic = $validated['topic'];
            }

            $session->save();

            // Update flashcard items if provided
            if (isset($validated['flashcard_ids'])) {
                // Delete existing items
                $session->items()->delete();

                // Create new items
                $flashcardIds = $validated['flashcard_ids'];
                $items = [];
                
                foreach ($flashcardIds as $position => $flashcardId) {
                    $items[] = [
                        'saved_session_id' => $session->id,
                        'flashcard_id' => $flashcardId,
                        'position' => $position + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $session->items()->createMany($items);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session đã được cập nhật thành công!',
                    'session' => $session->fresh(['items'])
                ]);
            }

            return redirect()->route('saved-sessions.show', $session->slug)
                ->with('success', 'Session đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra khi cập nhật session.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra khi cập nhật session.');
        }
    }

    /**
     * Remove the specified saved session from storage.
     *
     * @param string $slug
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(string $slug, Request $request)
    {
        $user = Auth::user();
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if user owns this session
        if ($session->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $session->delete(); // Items will be deleted via cascade

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session đã được xóa thành công!'
                ]);
            }

            return redirect()->route('saved-sessions.index')
                ->with('success', 'Session đã được xóa thành công!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra khi xóa session.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra khi xóa session.');
        }
    }

    /**
     * Start a review session based on a saved session.
     *
     * @param string $slug
     * @param ReviewSavedSessionRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function review(string $slug, ReviewSavedSessionRequest $request)
    {
        // Force check authentication
        if (!Auth::check()) {
            Log::warning('SavedSessionController::review - User not authenticated');
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Authentication required'], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to access saved sessions.');
        }
        
        $user = Auth::user();
        $validated = $request->validated();
        
        Log::info('SavedSessionController::review - Starting review', [
            'user_id' => $user->id,
            'slug' => $slug,
            'validated_data' => $validated,
        ]);
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $slug)
            ->with('items')
            ->firstOrFail();

        // Check if user owns this session
        if ($session->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get flashcard IDs
        $flashcardIds = $validated['shuffle'] 
            ? $session->getShuffledFlashcardIds()
            : $session->getFlashcardIds();

        if (empty($flashcardIds)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session này không có flashcard nào.'
                ], 400);
            }

            return back()->with('error', 'Session này không có flashcard nào.');
        }

        // Prepare data for starting flashcard session
        $flashcardData = [
            'mode' => 'saved_session',
            'saved_session_id' => $session->id,
            'flashcard_type' => $validated['flashcard_type'],
            'flashcard_ids' => $flashcardIds,
            'word_count' => count($flashcardIds),
            'shuffle' => $validated['shuffle'],
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Chuẩn bị bắt đầu review session...',
                'flashcard_data' => $flashcardData
            ]);
        }

        // Use POST request to start flashcard session
        try {
            // Create new request with flashcard data
            $flashcardRequest = Request::create(route('flashcards.start'), 'POST', $flashcardData);
            $flashcardRequest->setUserResolver(function () use ($user) {
                return $user;
            });
            
            // Start flashcard session using the FlashcardController
            $flashcardController = app(\App\Http\Controllers\FlashcardController::class);
            
            return $flashcardController->start($flashcardRequest);
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Có lỗi xảy ra khi bắt đầu review session.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra khi bắt đầu review session.');
        }
    }
}
