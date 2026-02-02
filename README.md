<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# Train Ticketing System

A web-based train booking application built with Laravel. This system allows users to search for trains, book tickets, and manage their user accounts, while administrators can manage train schedules and system settings.

## 📋 Requirements

Before you begin, ensure you have the following installed:

*   **PHP 8.2+**
*   **Composer**
*   **Node.js & NPM**
*   **MySQL or MariaDB**

## 🚀 Installation & Setup

1.  **Clone or Download**:
    Download the project files to your local machine.

2.  **Install Dependencies**:
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**:
    Copy `.env.example` to `.env` and configure your database credentials.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Setup**:
    Run migrations and seeders.
    ```bash
    php artisan migrate --seed
    ```

5.  **Build Assets**:
    ```bash
    npm run dev
    ```

6.  **Run Application**:
    ```bash
    php artisan serve
    ```
    Access the application at `http://localhost:8000`.

---

## 📖 Step-by-Step Usage Guide

### 1. Register Account
To access the booking features, create a new user account.

*   **Navigate**: Click on **Sign Up** in the navigation bar.
*   **Action**: Fill in the registration form.
*   **Test Credentials (Seeded)**:
    *   **Email Address**: `test@example.com`
    *   **Password**: `password`

### 2. Login Account
Access your account to book tickets.

*   **Navigate**: Click on **Sign In**.
*   **Action**: Enter your credentials.

### 3. Admin Access
To view the admin dashboard (train management, users, etc.).

*   **Note**: No default admin account is seeded. You can create one manually or using Tinker:
    ```bash
    php artisan tinker
    ```
    ```php
    User::factory()->create(['email' => 'admin@example.com', 'account_status' => 'admin']);
    ```
*   **Navigate**: Login with the admin account. You will be redirected to the Admin Dashboard.
*   **Test Credentials (Manual Setup)**:
    *   **Email Address**: `admin@example.com`
    *   **Password**: `password`

### 4. Search Train
Once logged in, search for available trains.

*   **Navigate**: Go to **Train Selection**.
*   **Action**: Select Depart/Return stations and dates.
*   **Stations**: e.g., `KL Sentral`, `Butterworth`, `Ipoh`.

## 🛠 Troubleshooting

*   **Database Error**: Ensure your `.env` file has the correct database credentials.
*   **Vite Manifest Not Found**: Run `npm run build` or keep `npm run dev` running.
