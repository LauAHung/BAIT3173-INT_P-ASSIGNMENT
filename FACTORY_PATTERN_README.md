# User Management Module - Factory Pattern Implementation

This document describes the implementation of the Factory Pattern for the User Management Module, replacing the previous Builder and Strategy patterns.

## Overview

The User Management Module has been refactored to use the Factory Pattern, which provides better separation of concerns and more flexible user creation and authentication processes.

## Factory Classes Structure

### 1. User Factories

#### Base Factory
- `UserFactory` (Abstract) - Base factory class with common user creation methods

#### Concrete Factories
- `RegularUserFactory` - Creates users with email/password authentication
- `FacebookUserFactory` - Creates users authenticated via Facebook
- `GoogleUserFactory` - Creates users authenticated via Google

#### Factory Manager
- `UserFactoryManager` - Manages different user factory types

### 2. Authentication Factories

#### Base Factory
- `AuthFactory` (Abstract) - Base authentication factory

#### Concrete Factories
- `EmailAuthFactory` - Handles email/password authentication
- `SocialAuthFactory` - Handles social media authentication

#### Factory Manager
- `AuthFactoryManager` - Manages different authentication factory types

### 3. Mail Factories

#### Base Factory
- `MailFactory` (Abstract) - Base mail factory

#### Concrete Factories
- `EmailVerificationMailFactory` - Sends email verification mails
- `PasswordResetMailFactory` - Sends password reset mails

#### Factory Manager
- `MailFactoryManager` - Manages different mail factory types

## Usage Examples

### Creating Users

```php
// Regular user creation
$user = $userFactoryManager->createUser('regular', [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'securepassword123',
    'newsletter' => true,
    'marketing' => false,
    'updates' => true
]);

// Facebook user creation
$user = $userFactoryManager->createUser('facebook', [
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'email' => 'jane@example.com',
    'provider_id' => '123456789',
    'provider_data' => ['facebook_data'],
    'profile_picture' => 'https://example.com/avatar.jpg'
]);

// Google user creation
$user = $userFactoryManager->createUser('google', [
    'first_name' => 'Bob',
    'last_name' => 'Johnson',
    'email' => 'bob@example.com',
    'provider_id' => '987654321',
    'provider_data' => ['google_data'],
    'profile_picture' => 'https://example.com/avatar.jpg'
]);
```

### Authentication

```php
// Email authentication
$user = $authFactoryManager->authenticate('email', [
    'email' => 'user@example.com',
    'password' => 'password123'
]);

// Social media authentication
$user = $authFactoryManager->authenticate('social', [
    'provider' => 'facebook',
    'provider_id' => '123456789'
]);
```

### Sending Mails

```php
// Send email verification
$success = $mailFactoryManager->sendMail('email_verification', $user, [
    'token' => 'verification_token_here'
]);

// Send password reset
$success = $mailFactoryManager->sendMail('password_reset', $user, [
    'token' => 'reset_token_here'
]);
```

## Controller Integration

All controllers have been updated to use the Factory pattern:

### SignupController
- Uses `UserFactoryManager` for user creation
- Uses `AuthFactoryManager` for OAuth authentication
- Uses `MailFactoryManager` for sending verification emails

### LoginController
- Uses `AuthFactoryManager` for email authentication
- Uses `MailFactoryManager` for password reset emails

### FacebookAuthController & GoogleAuthController
- Use `UserFactoryManager` for social media user creation
- Use `AuthFactoryManager` for social media authentication

### ProfileController
- Uses `UserFactoryManager` for user updates
- Uses `MailFactoryManager` for notification emails

## Benefits of Factory Pattern

1. **Separation of Concerns**: Each factory handles a specific type of user creation or authentication
2. **Extensibility**: Easy to add new user types or authentication methods
3. **Maintainability**: Clear structure and easy to modify individual components
4. **Testability**: Each factory can be tested independently
5. **Flexibility**: Different creation strategies for different user types

## Error Handling

All factories include comprehensive error handling:
- Input validation
- Database constraint checking
- Exception handling with meaningful error messages
- Error collection and reporting

## Service Provider Registration

The factories are registered in `FactoryServiceProvider` and automatically injected into controllers through Laravel's dependency injection system.

## Service Layer Updates

### UserRegistrationService
- Updated to use `UserFactoryManager` and `MailFactoryManager`
- Replaced Builder pattern with Factory pattern for user creation
- Added helper methods: `getFactoryTypeFromProvider()`, `determineUserType()`
- Updated email sending to use Factory pattern
- Maintained all existing functionality while improving code structure

### UserService
- Added `UserFactoryManager` dependency injection
- Added new methods for Factory pattern integration:
  - `getAvailableUserTypes()` - Get all supported user factory types
  - `validateUserData()` - Validate user data using appropriate factory
  - `getUserStatsByType()` - Get statistics by factory type
- Enhanced existing functionality with Factory pattern support

## Migration from Builder/Strategy Pattern

The following files were removed:
- `app/Builders/UserBuilder.php`
- `app/Models/Strategies/AuthStrategy.php`
- `app/Models/Strategies/AuthContext.php`
- `app/Models/Strategies/FacebookAuthStrategy.php`
- `app/Models/Strategies/GoogleAuthStrategy.php`

All functionality has been preserved and enhanced through the Factory pattern implementation.
