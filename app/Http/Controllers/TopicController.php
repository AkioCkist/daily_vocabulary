<?php

namespace App\Http\Controllers;

use App\Services\TopicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TopicController extends Controller
{
    public function __construct(
        private TopicService $topicService
    ) {}

    /**
     * Get all topics for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $topics = $this->topicService->getAllAvailableTopics($user);

        return response()->json($topics);
    }

    /**
     * Create a new custom topic.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $user = Auth::user();
            $topic = $this->topicService->createUserTopic($user, $request->only(['name', 'description']));

            return back()->with('success', 'Topic created successfully');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Update a custom topic.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $user = Auth::user();
            $topic = $this->topicService->updateUserTopic($user, $id, $request->only(['name', 'description']));

            return back()->with('success', 'Topic updated successfully');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Delete a custom topic.
     */
    public function destroy(int $id)
    {
        try {
            $user = Auth::user();
            $this->topicService->deleteUserTopic($user, $id);

            return back()->with('success', 'Topic deleted successfully');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Get suggested topics.
     */
    public function suggested(): JsonResponse
    {
        $topics = $this->topicService->getSuggestedTopics();

        return response()->json($topics);
    }

    /**
     * Search topics.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
        ]);

        $user = Auth::user();
        $topics = $this->topicService->searchTopics($user, $request->get('q'));

        return response()->json($topics);
    }
}