# Dooeats Restaurant Dashboard

## Overview
The Restaurant Dashboard is a dedicated portal for restaurant partners to manage their business operations on the Dooeats platform. It provides tools for menu management, order processing, and subscription handling.

## Functionalities
- **Order Management**:
  - View Pending, Accepted, Rejected, and Completed orders.
  - Print order details.
  - Real-time order status updates.
- **Menu Management**:
  - Manage Foods, Categories, and Attributes (Add-ons).
  - Manage Coupons specific to the restaurant.
- **Business Operations**:
  - **Dine-In**: Manage table bookings (`/booktable`).
  - **Earnings & Payouts**: View earnings, request payouts, and manage withdrawal methods.
  - **Staff**: Manage delivery men assigned to the restaurant.
- **Subscription System**:
  - View and purchase subscription plans.
  - Manage subscription history.
- **Advertisement**: Create and manage advertisements.

## Tech Stack
- **Backend**: Laravel 10.x
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: MySQL
- **Payment**: Paystack (for subscriptions).

## Payment Methods
- **Paystack**: Used for purchasing subscription plans and processing order payments.
- **Wallet**: Internal wallet for transactions.

## Installation & Setup

1. **Navigate**: `cd restaurant`
2. **Install**:
   ```bash
   composer install
   npm install
   ```
3. **Environment**:
   ```bash
   cp .env.example .env
   # Configure DB and Firebase credentials
   php artisan key:generate
   ```
4. **Run**:
   ```bash
   php artisan serve --port=8002
   npm run watch
   ```
