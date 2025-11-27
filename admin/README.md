# Dooeats Admin Dashboard

## Overview
The Admin Dashboard is the central control hub for the Dooeats platform. It allows super admins to manage users, vendors (restaurants), drivers, finances, and global settings.

## Functionalities
- **User Management**: View, edit, and manage customers and admin users.
- **Vendor Management**:
  - Approve/Reject restaurant applications.
  - Manage restaurant details, menus, and payouts.
- **Driver Management**:
  - Approve/Reject driver applications.
  - Manage driver documents and payouts.
- **Order Management**: View all orders, print orders, and track status.
- **Financials**:
  - Manage payouts for Restaurants and Drivers.
  - View wallet transactions.
  - Configure commission rates and tax settings.
- **Content Management (CMS)**: Manage pages, terms, privacy policy.
- **Marketing**:
  - Create and manage Coupons.
  - Manage Banners and Special Offers.
  - Send push notifications (Broadcast).
- **Settings**:
  - Global app settings (Currencies, Languages, Radius).
  - Payment method configuration (Paystack).
  - Zone management.

## Tech Stack
- **Backend**: Laravel 10.x
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: MySQL (Shared with Main App)
- **Libraries**:
  - `spatie/laravel-permission`: For Role-Based Access Control (RBAC).
  - `firebase-admin`: For managing Firebase interactions.

## APIs & Services
- **Firebase**: For sending notifications to apps.
- **Paystack**: For processing platform payments.

## Installation & Setup

1. **Navigate**: `cd admin`
2. **Install**:
   ```bash
   composer install
   npm install
   ```
3. **Environment**:
   ```bash
   cp .env.example .env
   # Ensure DB credentials match the Main App
   php artisan key:generate
   ```
4. **Run**:
   ```bash
   php artisan serve --port=8001
   npm run watch
   ```
