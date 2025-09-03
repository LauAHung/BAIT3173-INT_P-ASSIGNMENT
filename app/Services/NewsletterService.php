<?php

namespace App\Services;

use App\Models\User;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Exception;

class NewsletterService
{
    /**
     * Send newsletter to users
     */
    public function sendNewsletter($newsletterData)
    {
        try {
            $subject = $newsletterData['subject'] ?? 'Newsletter';
            $content = $newsletterData['content'] ?? '';
            $recipients = $newsletterData['recipients'] ?? 'all'; // all, active, newsletter_subscribers

            // Get recipients based on criteria
            $users = $this->getRecipients($recipients);

            if ($users->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No recipients found'
                ];
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($users as $user) {
                try {
                    // Send email to each user
                    Mail::to($user->email)->send(new \App\Mail\NewsletterMail($subject, $content, $user));
                    $sentCount++;
                } catch (Exception $e) {
                    $failedCount++;
                }
            }

            return [
                'success' => true,
                'message' => "Newsletter sent successfully",
                'data' => [
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                    'total_recipients' => $users->count()
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send newsletter: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get newsletter statistics
     */
    public function getNewsletterStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'newsletter_subscribers' => User::where('email_subscription->newsletter', true)->count(),
                'active_users' => User::where('account_status', 'active')->count(),
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'social_users' => User::whereNotNull('social_provider')->count(),
                'users_by_provider' => User::whereNotNull('social_provider')
                    ->selectRaw('social_provider, count(*) as count')
                    ->groupBy('social_provider')
                    ->get(),
            ];

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get newsletter stats: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get recipients based on criteria
     */
    private function getRecipients($criteria)
    {
        switch ($criteria) {
            case 'all':
                return User::all();
            case 'active':
                return User::where('account_status', 'active')->get();
            case 'newsletter_subscribers':
                // Merge internal users opted in and external subscribers
                $internal = User::where('email_subscription->newsletter', true)->pluck('email')->toArray();
                $external = NewsletterSubscriber::pluck('email')->toArray();
                $emails = array_unique(array_merge($internal, $external));
                return collect(array_map(function ($email) {
                    $obj = new \stdClass();
                    $obj->email = $email;
                    return $obj;
                }, $emails));
            case 'verified':
                return User::whereNotNull('email_verified_at')->get();
            default:
                return collect();
        }
    }

    /**
     * Update user newsletter subscription
     */
    public function updateNewsletterSubscription($userId, $subscribed)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            $subscription = $user->email_subscription ?? [];
            $subscription['newsletter'] = $subscribed;
            $user->email_subscription = $subscription;
            $user->save();

            return [
                'success' => true,
                'message' => 'Newsletter subscription updated successfully',
                'data' => $user
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update newsletter subscription: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get newsletter templates
     */
    public function getNewsletterTemplates()
    {
        try {
            $templates = [
                'welcome' => [
                    'subject' => 'Welcome to TravelFree!',
                    'content' => 'Welcome to our platform. We\'re excited to have you on board!'
                ],
                'promotion' => [
                    'subject' => 'Special Promotion - Limited Time!',
                    'content' => 'Don\'t miss out on our special promotion. Book now and save!'
                ],
                'update' => [
                    'subject' => 'Platform Update',
                    'content' => 'We\'ve made some improvements to our platform. Check them out!'
                ]
            ];

            return [
                'success' => true,
                'data' => $templates
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get newsletter templates: ' . $e->getMessage()
            ];
        }
    }
} 