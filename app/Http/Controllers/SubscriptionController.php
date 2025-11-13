<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function store(Request $request, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $subscriptionService->subscribe($validated['email']);

        return redirect()->back()->with('success', 'Check your email to confirm subscription!');
    }
}

