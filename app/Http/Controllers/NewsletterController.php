<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            ['subscribed_at' => now()]
        );

        // Send confirmation email
        try {
            Mail::to($subscriber->email)->send(new \App\Mail\NewsletterSubscribedMail());
        } catch (\Throwable $e) {
            // Ignore email send error; still report subscribed
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully',
            'data' => [
                'email' => $subscriber->email,
                'subscribed_at' => $subscriber->subscribed_at,
            ]
        ]);
    }

    public function list(Request $request)
    {
        $search = $request->get('search');

        // External subscribers
        $external = NewsletterSubscriber::when($search, function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%");
        })->get(['email', 'subscribed_at']);

        // Internal users who opted in
        $internalUsers = \App\Models\User::where('email_subscription->newsletter', true)
            ->when($search, function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            })
            ->get(['email', 'created_at']);

        // Merge unique by email; prefer external subscribed_at if available
        $map = [];
        foreach ($internalUsers as $u) {
            $map[$u->email] = [
                'email' => $u->email,
                'subscribed_at' => $u->created_at,
                'source' => 'internal'
            ];
        }
        foreach ($external as $e) {
            $map[$e->email] = [
                'email' => $e->email,
                'subscribed_at' => $e->subscribed_at,
                'source' => 'external'
            ];
        }

        $list = array_values($map);
        usort($list, function ($a, $b) {
            return strcmp((string)($b['subscribed_at'] ?? ''), (string)($a['subscribed_at'] ?? ''));
        });

        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // Remove from external list if exists
        NewsletterSubscriber::where('email', $email)->delete();

        // Update internal user preference if exists
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $prefs = $user->email_subscription ?? [];
            $prefs['newsletter'] = false;
            $user->email_subscription = $prefs;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed successfully'
        ]);
    }
}


