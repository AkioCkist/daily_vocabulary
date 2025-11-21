<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SavedSession;
use App\Models\SavedSessionItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Controller for managing individual items within saved sessions.
 * 
 * Handles adding, removing, and reordering flashcards within saved sessions.
 */
class SavedSessionItemController extends Controller
{
    /**
     * Add a new flashcard to a saved session.
     *
     * @param string $sessionSlug
     * @param Request $request
     * @return JsonResponse
     */
    public function store(string $sessionSlug, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $sessionSlug)
            ->firstOrFail();

        $request->validate([
            'flashcard_id' => ['required', 'integer', 'min:1'],
            'position' => ['nullable', 'integer', 'min:1'],
        ], [
            'flashcard_id.required' => 'Flashcard ID là bắt buộc.',
            'flashcard_id.integer' => 'Flashcard ID phải là số nguyên.',
            'flashcard_id.min' => 'Flashcard ID phải lớn hơn 0.',
            'position.integer' => 'Vị trí phải là số nguyên.',
            'position.min' => 'Vị trí phải lớn hơn 0.',
        ]);

        // Check if flashcard already exists in this session
        $existingItem = SavedSessionItem::forSession($session->id)
            ->where('flashcard_id', $request->flashcard_id)
            ->first();

        if ($existingItem) {
            return response()->json([
                'message' => 'Flashcard này đã có trong session.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $position = $request->position;
            
            // If no position specified, add at the end
            if (!$position) {
                $maxPosition = SavedSessionItem::forSession($session->id)->max('position') ?? 0;
                $position = $maxPosition + 1;
            } else {
                // Shift existing items to make room
                SavedSessionItem::forSession($session->id)
                    ->where('position', '>=', $position)
                    ->increment('position');
            }

            // Create new item
            $item = SavedSessionItem::create([
                'saved_session_id' => $session->id,
                'flashcard_id' => $request->flashcard_id,
                'position' => $position,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Flashcard đã được thêm vào session.',
                'item' => $item
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm flashcard.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a flashcard from a saved session.
     *
     * @param string $sessionSlug
     * @param int $itemId
     * @return JsonResponse
     */
    public function destroy(string $sessionSlug, int $itemId): JsonResponse
    {
        $user = Auth::user();
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $sessionSlug)
            ->firstOrFail();

        $item = SavedSessionItem::forSession($session->id)
            ->where('id', $itemId)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $removedPosition = $item->position;
            
            // Delete the item
            $item->delete();

            // Shift remaining items down
            SavedSessionItem::forSession($session->id)
                ->where('position', '>', $removedPosition)
                ->decrement('position');

            DB::commit();

            return response()->json([
                'message' => 'Flashcard đã được xóa khỏi session.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa flashcard.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder items in a saved session.
     *
     * @param string $sessionSlug
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(string $sessionSlug, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $sessionSlug)
            ->firstOrFail();

        $request->validate([
            'item_orders' => ['required', 'array'],
            'item_orders.*.id' => ['required', 'integer', 'exists:saved_session_items,id'],
            'item_orders.*.position' => ['required', 'integer', 'min:1'],
        ], [
            'item_orders.required' => 'Thứ tự items là bắt buộc.',
            'item_orders.array' => 'Thứ tự items phải là mảng.',
            'item_orders.*.id.required' => 'ID item là bắt buộc.',
            'item_orders.*.id.exists' => 'Item không tồn tại.',
            'item_orders.*.position.required' => 'Vị trí là bắt buộc.',
            'item_orders.*.position.min' => 'Vị trí phải lớn hơn 0.',
        ]);

        try {
            DB::beginTransaction();

            $itemOrders = $request->item_orders;
            
            // Validate that all items belong to this session
            $itemIds = collect($itemOrders)->pluck('id');
            $validItems = SavedSessionItem::forSession($session->id)
                ->whereIn('id', $itemIds)
                ->pluck('id');

            if ($validItems->count() !== $itemIds->count()) {
                return response()->json([
                    'message' => 'Một số items không thuộc session này.',
                ], 400);
            }

            // Update positions
            foreach ($itemOrders as $order) {
                SavedSessionItem::where('id', $order['id'])
                    ->update(['position' => $order['position']]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Thứ tự flashcards đã được cập nhật.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Có lỗi xảy ra khi sắp xếp lại flashcards.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move a single item to a new position.
     *
     * @param string $sessionSlug
     * @param int $itemId
     * @param Request $request
     * @return JsonResponse
     */
    public function move(string $sessionSlug, int $itemId, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $session = SavedSession::forUser($user->id)
            ->where('slug', $sessionSlug)
            ->firstOrFail();

        $item = SavedSessionItem::forSession($session->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $request->validate([
            'new_position' => ['required', 'integer', 'min:1'],
        ], [
            'new_position.required' => 'Vị trí mới là bắt buộc.',
            'new_position.integer' => 'Vị trí phải là số nguyên.',
            'new_position.min' => 'Vị trí phải lớn hơn 0.',
        ]);

        $newPosition = $request->new_position;
        $oldPosition = $item->position;

        if ($newPosition === $oldPosition) {
            return response()->json([
                'message' => 'Vị trí không thay đổi.',
            ]);
        }

        try {
            DB::beginTransaction();

            if ($newPosition > $oldPosition) {
                // Moving down: shift items up
                SavedSessionItem::forSession($session->id)
                    ->where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->decrement('position');
            } else {
                // Moving up: shift items down
                SavedSessionItem::forSession($session->id)
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->increment('position');
            }

            // Update the item's position
            $item->update(['position' => $newPosition]);

            DB::commit();

            return response()->json([
                'message' => 'Vị trí flashcard đã được cập nhật.',
                'item' => $item->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Có lỗi xảy ra khi di chuyển flashcard.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
