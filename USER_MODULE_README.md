# User Module - Builder Pattern Implementation

## Overview

This User Module implements the **Builder Design Pattern** in Laravel to provide a comprehensive user management system with the following functionalities:

- **Registration**: Standard user registration with email verification
- **Login**: Traditional email/password authentication
- **Social Media Login**: Google and Facebook OAuth integration
- **Forgot Password**: Secure password reset functionality
- **Profile Management**: Complete user profile management
- **Password Hashing Encryption**: Secure password handling
- **Email Subscription**: Configurable email preferences

## 🚀 **Key Feature: Works with Any User Data**

The User Module is designed to be **completely flexible** and can work with any user data without relying on predefined values. It includes:

- **Robust Validation**: Comprehensive validation for all user data
- **Flexible Data Handling**: Accepts any data format and maps it appropriately
- **Error Handling**: Clear error messages for invalid data
- **Dynamic Configuration**: Automatically determines user type and sets appropriate defaults
- **External Data Support**: Can handle data from external sources with field mapping

## Design Pattern Rationale

The **Builder Pattern** was chosen for this module because:

1. **Complex Object Construction**: User objects have multiple attributes (name, email, password, profile picture, subscription preferences, social media info, etc.)
2. **Security Requirements**: Password hashing and OTP validation need to be applied consistently
3. **Flexible Configuration**: Different user types (standard, social media, admin, premium) require different configurations
4. **Avoiding Singleton Limitations**: The Builder pattern allows multiple user instances with different configurations
5. **Step-by-step Construction**: Ensures all required fields are set and security measures are applied consistently

## Architecture

### Core Components

#### 1. UserBuilder (`app/Builders/UserBuilder.php`)
The main builder class that constructs User objects step-by-step with comprehensive validation:

```php
$user = (new UserBuilder())
    ->setBasicInfo('John', 'Doe', 'john@example.com')
    ->setPassword('securepassword123')
    ->setEmailSubscription(true, false, true)
    ->setProfilePicture('/images/avatar.jpg')
    ->setSocialMediaInfo('google', 'google_123456')
    ->setEmailVerified(true)
    ->setAccountStatus('active')
    ->build();
```

**Key Methods:**
- `setBasicInfo()`: Set name and email with validation
- `setPassword()`: Hash and set password with strength validation
- `setSocialMediaPassword()`: Generate random password for social users
- `setProfilePicture()`: Set profile picture URL with validation
- `setEmailSubscription()`: Configure email preferences
- `setSocialMediaInfo()`: Set social media provider details with validation
- `setEmailVerified()`: Set email verification status
- `setAccountStatus()`: Set account status with validation
- `setLastLogin()`: Update last login timestamp
- `setCustomData()`: Set additional custom fields with protection
- `build()`: Create and return the User instance with validation
- `reset()`: Reset builder for reuse
- `createFromArray()`: Create user from any array data
- `buildForValidation()`: Validate data without saving

#### 2. UserRegistrationService (`app/Services/UserRegistrationService.php`)
The Director class that orchestrates the building process with robust error handling:

```php
// Standard registration
$user = $userRegistrationService->registerUser($userData);

// Social media registration
$user = $userRegistrationService->registerSocialMediaUser($socialData);

// Create from any array data
$user = $userRegistrationService->createUserFromArray($anyUserData);

// Profile update
$user = $userRegistrationService->updateUserProfile($user, $profileData);

// Password reset
$success = $userRegistrationService->handleForgotPassword($email);
```

**Key Methods:**
- `registerUser()`: Standard user registration with validation
- `registerSocialMediaUser()`: Social media user registration with validation
- `createUserFromArray()`: Create user from any array data
- `updateUserProfile()`: Update existing user profile with validation
- `handleUserLogin()`: Update last login timestamp
- `handleForgotPassword()`: Initiate password reset with validation
- `resetPassword()`: Reset password with token validation
- `changePassword()`: Change password for authenticated user
- `verifyEmail()`: Verify email address
- `updateEmailSubscription()`: Update email preferences
- `createUserWithCustomConfig()`: Create user with custom builder configuration
- `validateUserDataOnly()`: Validate data without creating user

#### 3. Enhanced User Model (`app/Models/User.php`)
Extended User model with additional fields and helper methods:

**New Fields:**
- `profile_picture`: User profile picture URL
- `social_provider`: Social media provider (google, facebook)
- `social_provider_id`: Provider-specific user ID
- `social_provider_data`: JSON data from social provider
- `email_subscription`: JSON email preferences
- `email_verification_token`: Email verification token
- `account_status`: Account status (active, pending_verification, deleted)
- `last_login_at`: Last login timestamp
- `password_reset_token`: Password reset token
- `password_reset_expires_at`: Password reset token expiry

**Helper Methods:**
- `getFullNameAttribute()`: Get user's full name
- `isVerified()`: Check if email is verified
- `isActive()`: Check if account is active
- `hasSocialLogin()`: Check if user has social login
- `getEmailSubscriptionPreferences()`: Get email preferences
- `isSubscribedTo()`: Check specific email subscription

**Query Scopes:**
- `scopeActive()`: Get only active users
- `scopeVerified()`: Get only verified users
- `scopeBySocialProvider()`: Get users by social provider

## 🎯 **Flexible Usage Examples**

### 1. User Authentication (Login/Logout)

```php
// Login with email and password
$credentials = [
    'email' => 'user@example.com',
    'password' => 'SecurePass123'
];

if (Auth::attempt($credentials, $remember = true)) {
    $user = Auth::user();
    
    // Update last login timestamp
    $userRegistrationService->handleUserLogin($user);
    
    // Check account status
    if ($user->isActive()) {
        return redirect()->route('dashboard');
    } else {
        Auth::logout();
        return back()->withErrors(['email' => 'Account not active']);
    }
}

// Logout
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

### 2. Password Reset Functionality

```php
// Request password reset
$success = $userRegistrationService->handleForgotPassword('user@example.com');

// Reset password with token
$success = $userRegistrationService->resetPassword($token, $newPassword);

// Change password for authenticated user
$success = $userRegistrationService->changePassword($user, $currentPassword, $newPassword);
```

### 3. Email Verification

```php
// Verify email with token
$success = $userRegistrationService->verifyEmail($token);

// Check if user is verified
if ($user->isVerified()) {
    // User is verified
}
```

### 4. Create User from Any Data

```php
// Minimal data
$userData = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123'
];

$user = $userRegistrationService->createUserFromArray($userData);

// Full data with social media
$fullData = [
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'email' => 'jane@example.com',
    'password' => 'SecurePass456',
    'profile_picture' => 'https://example.com/avatar.jpg',
    'social_provider' => 'google',
    'social_provider_id' => 'google_123456',
    'social_provider_data' => ['given_name' => 'Jane'],
    'newsletter' => true,
    'marketing' => false,
    'updates' => true,
    'account_status' => 'active',
    'email_verified' => true,
    'custom_data' => [
        'role' => 'user',
        'preferences' => json_encode(['theme' => 'dark'])
    ]
];

$user = $userRegistrationService->createUserFromArray($fullData);
```

### 5. Handle External Data Sources

```php
// External data with different field names
$externalData = [
    'name' => 'John',
    'surname' => 'Doe',
    'email_address' => 'john@example.com',
    'pass' => 'SecurePass123',
    'avatar' => 'https://example.com/avatar.jpg',
    'provider' => 'google',
    'provider_id' => 'google_123456',
    'verified' => true,
    'status' => 'active'
];

$user = $flexibleExample->handleExternalUserData($externalData);
```

### 6. Bulk User Creation

```php
$usersData = [
    [
        'first_name' => 'User1',
        'last_name' => 'Test',
        'email' => 'user1@example.com',
        'password' => 'SecurePass123'
    ],
    [
        'first_name' => 'User2',
        'last_name' => 'Test',
        'email' => 'user2@example.com',
        'password' => 'SecurePass456',
        'social_provider' => 'facebook',
        'social_provider_id' => 'fb_123456'
    ]
];

$results = $flexibleExample->createBulkUsers($usersData);
```

### 7. Validate Data Without Creating User

```php
$userData = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'password' => 'SecurePass123'
];

$validatedData = $userRegistrationService->validateUserDataOnly($userData);
```

### 8. Dynamic User Creation

```php
$inputData = [
    'first_name' => 'Dynamic',
    'last_name' => 'User',
    'email' => 'dynamic@example.com',
    'social_provider' => 'google',
    'social_provider_id' => 'google_789012'
];

// System automatically determines user type and sets appropriate defaults
$user = $flexibleExample->createUserDynamically($inputData);
```

## 🔒 **Validation & Security Features**

### Input Validation
- **Email Validation**: Ensures valid email format
- **Password Strength**: Minimum 8 characters with uppercase, lowercase, and number
- **Password Confirmation**: Optional password confirmation with validation
- **Required Fields**: Validates all required fields are present
- **Social Provider Validation**: Validates supported social media providers
- **Profile Picture Validation**: Validates URL or file path format
- **Account Status Validation**: Validates valid account statuses

### Security Features
1. **Password Hashing**: All passwords are hashed using Laravel's Hash facade
2. **Token Security**: Secure tokens for email verification and password reset
3. **Account Status**: Multiple account statuses for security management
4. **Social Media Security**: Secure handling of social media data
5. **Email Verification**: Email verification for standard registrations
6. **Protected Fields**: Prevents overriding of protected database fields

### Error Handling
- **Comprehensive Validation**: Validates all input data
- **Clear Error Messages**: Provides specific error messages for each validation failure
- **Exception Handling**: Proper exception handling with meaningful messages
- **Data Cleaning**: Automatically cleans and trims input data

## 🧪 **Testing Flexibility**

The system includes comprehensive testing examples:

```php
$testResults = $flexibleExample->testSystemFlexibility();

// Tests include:
// - Empty data handling
// - Invalid email validation
// - Weak password validation
// - Valid data processing
```

## Database Migration

The module includes a migration that adds all necessary fields to the users table:

```bash
php artisan migrate
```

This will add the following fields to the users table:
- `profile_picture`
- `social_provider`
- `social_provider_id`
- `social_provider_data`
- `email_subscription`
- `email_verification_token`
- `account_status`
- `last_login_at`
- `password_reset_token`
- `password_reset_expires_at`

## Service Provider

The `UserServiceProvider` registers the dependencies for dependency injection:

```php
// In app/Providers/UserServiceProvider.php
$this->app->singleton(UserBuilder::class, function ($app) {
    return new UserBuilder();
});

$this->app->singleton(UserRegistrationService::class, function ($app) {
    return new UserRegistrationService($app->make(UserBuilder::class));
});
```

## Controllers

### Updated Controllers

1. **SignupController**: Now uses UserRegistrationService for registration
2. **GoogleAuthController**: Uses Builder pattern for social media registration
3. **FacebookAuthController**: Uses Builder pattern for social media registration

### New Controllers

1. **LoginController**: Handles user authentication, login, logout, password reset, and email verification
2. **ProfileController**: Handles profile management, password changes, and account deletion

## Benefits of the Builder Pattern

1. **Flexibility**: Easy to create users with different configurations
2. **Security**: Consistent password hashing and security measures
3. **Maintainability**: Clear separation of concerns
4. **Reusability**: Builder can be reused for different user types
5. **Validation**: Step-by-step validation during construction
6. **Extensibility**: Easy to add new user attributes and methods
7. **Robustness**: Handles any user data without predefined requirements

## Error Handling

The implementation includes comprehensive error handling:

- Validation of required fields
- Password strength requirements
- Email uniqueness validation
- Token expiration handling
- Social media provider validation
- Clear error messages for each validation failure
- Exception handling with meaningful messages

## Security Features

1. **Password Hashing**: All passwords are hashed using Laravel's Hash facade
2. **Token Security**: Secure tokens for email verification and password reset
3. **Account Status**: Multiple account statuses for security management
4. **Social Media Security**: Secure handling of social media data
5. **Email Verification**: Email verification for standard registrations
6. **Protected Fields**: Prevents overriding of protected database fields

## Testing

The module includes comprehensive functionality for:
- Standard user creation
- Social media user creation
- Custom user configurations
- Error handling
- Multiple user creation scenarios
- Handling any user data format
- External data source integration
- Bulk user creation
- Dynamic user type determination

You can test the functionality using the controllers and routes provided.

## Future Enhancements

1. **Two-Factor Authentication**: Add 2FA support
2. **Activity Logging**: Track user activities
3. **Role-Based Access Control**: Implement RBAC
4. **API Authentication**: Add API token support
5. **Advanced Social Login**: Support for more social providers
6. **Data Import/Export**: Bulk data import/export functionality
7. **Advanced Validation Rules**: Custom validation rules support

## Conclusion

The Builder pattern implementation provides a **robust, flexible, and secure** user management system that can handle **any user data** without relying on predefined values. The step-by-step construction process ensures all security measures are applied consistently, and the Director pattern provides a clean interface for different user creation scenarios.

The system is designed to be **production-ready** and can handle real-world scenarios where user data comes from various sources with different formats and requirements. 