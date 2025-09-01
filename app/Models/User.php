<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    public $timestamps = true;

    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'password',
        'gender',
        'date_of_birth',
        'profile_picture',
        'social_provider',
        'social_provider_id',
        'social_provider_data',
        'email_subscription',
        'email_verified_at',
        'email_verification_token',
        'account_status',
        'last_login_at',
        'password_reset_token',
        'password_reset_expires_at',
        'wallet_balance'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
        'email_verification_token',
        'password_reset_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_reset_expires_at' => 'datetime',
        'email_subscription' => 'array',
        'social_provider_data' => 'array'
    ];

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    /**
     * Get badge text and class for status/email verification
     */
    public function getStatusBadge(): array
    {
        if ($this->account_status === 'suspended') {
            return ['text' => 'SUSPENDED', 'class' => 'status-badge suspended'];
        }
        if ($this->account_status === 'admin') {
            return ['text' => 'ADMIN', 'class' => 'status-badge admin'];
        }
        if ($this->isVerified()) {
            return ['text' => 'Verified', 'class' => 'status-badge delivered'];
        }
        return ['text' => 'Not Verified', 'class' => 'status-badge pending'];
    }

    /**
     * Check if user has social media login
     */
    public function hasSocialLogin(): bool
    {
        return !is_null($this->social_provider);
    }

    /**
     * Check if user has a password set
     */
    public function hasPassword(): bool
    {
        return !empty($this->password) && $this->password !== 'social_login_no_password';
    }

    /**
     * Check if user needs to set a password
     */
    public function needsPasswordSetup(): bool
    {
        return $this->hasSocialLogin() && !$this->hasPassword();
    }

    /**
     * Get email subscription preferences
     */
    public function getEmailSubscriptionPreferences(): array
    {
        return $this->email_subscription ?? [
            'newsletter' => false,
            'marketing' => false,
            'updates' => false
        ];
    }

    /**
     * Check if user is subscribed to specific email type
     */
    public function isSubscribedTo(string $type): bool
    {
        $preferences = $this->getEmailSubscriptionPreferences();
        return $preferences[$type] ?? false;
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function createNewUser($data)
    {
        return self::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
        ]);
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    /**
     * Scope to get only verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to get users by social provider
     */
    public function scopeBySocialProvider($query, string $provider)
    {
        return $query->where('social_provider', $provider);
    }
}
