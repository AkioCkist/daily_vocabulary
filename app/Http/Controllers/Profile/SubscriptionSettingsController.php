<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SubscriptionSettingsController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'receive_ads' => ['sometimes','boolean'],
            'incorrect_words_frequency' => ['required', Rule::in(['none','weekly','monthly'])],
            'topic_summary_frequency' => ['required', Rule::in(['none','weekly','monthly'])],
        ]);

        /** @var Subscription $subscription */
        $subscription = Subscription::firstOrCreate(
            ['user_id' => $user->id],
            ['email' => $user->email]
        );

        $wasUnconfirmed = !$subscription->confirmed_at;
        $subscription->fill($data);
        if ($wasUnconfirmed) {
            $subscription->confirmed_at = now();
        }
        $subscription->save();

        // Send welcome email if this is the user's first subscription (confirmed_at was previously null)
        if ($wasUnconfirmed) {
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeSubscriptionMail($user, $subscription));
                DB::table('email_logs')->insert([
                    'user_id' => $user->id,
                    'type' => 'welcome',
                    'subject' => 'Welcome to Daily Vocabulary!',
                    'meta' => json_encode(['email' => $user->email]),
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // ignore failures
            }
        }

        // If the request came from an Inertia form submission, return a redirect
        // so Inertia gets a valid response instead of plain JSON.
        if ($request->header('X-Inertia')) {
            return redirect()->route('profile.edit')
                ->with('subscriptionUpdated', true);
        }

        return response()->json(['message' => 'Subscription preferences updated.']);
    }

    public function metrics(Request $request)
    {
        $user = Auth::user();

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $monthlyCount = DB::table('email_logs')
            ->where('user_id', $user->id)
            ->whereBetween('sent_at', [$start, $end])
            ->count();

        return response()->json([
            'monthly_email_count' => $monthlyCount,
        ]);
    }
}
