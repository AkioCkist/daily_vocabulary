<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function store(Request $request, SubscriptionService $subscriptionService)
    {
        \Illuminate\Support\Facades\Log::info('Subscription request received', ['request' => $request->all()]);
        
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        \Illuminate\Support\Facades\Log::info('Email validated', ['email' => $validated['email']]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $userId = $user ? $user->id : null;

        $result = $subscriptionService->subscribe($validated['email'], $userId);
        
        \Illuminate\Support\Facades\Log::info('Subscription created', ['subscription' => $result]);

        return redirect()->back()->with('success', 'You are now subscribed to Daily Vocabulary!');
    }

    public function unsubscribe(Request $request, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $subscriptionService->unsubscribe($validated['email']);

        return redirect()->back()->with('success', 'You have been unsubscribed successfully!');
    }

    public function checkStatus(Request $request, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $isSubscribed = $subscriptionService->isSubscribed($validated['email']);

        return response()->json(['subscribed' => $isSubscribed]);
    }

    public function getAuthUserStatus(SubscriptionService $subscriptionService)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user) {
            return response()->json(['subscribed' => false, 'email' => null]);
        }

        $isSubscribed = $subscriptionService->isSubscribed($user->email);

        return response()->json(['subscribed' => $isSubscribed, 'email' => $user->email]);
    }
}

