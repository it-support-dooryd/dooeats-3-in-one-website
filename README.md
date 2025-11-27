# Dooeats Main Web App

## Overview
This is the main web application for the Dooeats platform, serving as the customer-facing interface for ordering food, managing profiles, and tracking orders. It is built with Laravel and Vue.js, featuring a modern "Glassmorphism" design aesthetic.

## Functionalities
- **User Authentication**: Login, Signup, Social Signup, Forgot Password.
- **Restaurant Discovery**: Browse restaurants, filter by category, view trending spots.
- **Ordering System**:
  - Add items to cart (with attributes/addons).
  - Checkout with delivery or takeaway options.
  - Real-time order tracking (Pending, Accepted, Completed, Cancelled).
  - Re-ordering from history.
- **Wallet System**: Top-up wallet, pay via wallet.
- **Profile Management**: Manage delivery addresses, favorites (stores & products).
- **Gift Cards**: Purchase and redeem gift cards.
- **Dine-In**: Book tables and manage dine-in orders.
- **Localization**: Multi-language support.

## Styling & Design
The application uses a **Glassmorphism** design language with a dark mode base.
- **Core Colors**:
  - Primary: `#047857` (Green)
  - Secondary: `#EF4444` (Red)
  - Background: `#121212` (Dark)
  - Card Background: `#1E1E1E`
  - Text: `#FFFFFF` (Main), `#B0B0B0` (Muted)
- **Design Elements**:
  - Glass effects (`rgba(30, 30, 30, 0.8)` background with blur).
  - Rounded corners and smooth transitions.
  - Font Family: 'Montserrat', sans-serif.

## Tech Stack
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Vue.js 2.x, Bootstrap 5, jQuery
- **Database**: MySQL
- **APIs & Services**:
  - **Firebase**: For real-time notifications and services.
  - **Google Maps API**: For location services (`google/apiclient`).
  - **Paystack**: Primary payment gateway.

## Payment Methods
- **Primary Gateway**: **Paystack** (`perfectmak/paystack-php`).
- **Wallet**: Internal wallet system.
- **Deprecated Gateways**: Razorpay, PayPal, Stripe, Flutterwave, MercadoPago, etc. (Routes return 410 Gone).

## Running the Application

### Development
To run the application locally with hot-reloading for frontend assets:

1. **Start the Laravel Development Server**:
   ```bash
   php artisan serve
   ```

2. **Compile Assets (Watch Mode)**:
   In a separate terminal, run:
   ```bash
   npm run watch
   ```

Access the application at `http://localhost:8000`.

### Production Build
To compile assets for production:
```bash
npm run prod
```

## Public Information & Business Rules (Scraped from dooeats.com)
- **Tagline**: "eat what you want, where you want..."
- **Company**: Dooryd Enterprise Limited
- **Service Area**: Currently serving every part of **Calabar**.
- **Delivery Time**: Estimated 15–30 minutes.
- **Payment Rules**:
  - **No Cash Payments**.
  - Payments via **Paystack** gateway.
  - Official accounts: Paystack Titan or Dooryd Enterprise Limited GT Bank.
- **Ordering Rules**:
  - **Single Restaurant Policy**: Users cannot order from multiple restaurants in a single cart. Separate orders are required.
  - **Refunds**: Processed within 7 business days.
- **User Flow**:
  1. Choose Location.
  2. Select Items & Customize.
  3. Secure Payment (Paystack).
  4. Order Confirmation.

## Folder Structure
- `app/`: Core application code (Models, Controllers, etc.).
- `resources/js/`: Vue.js components and application logic.
- `resources/sass/`: Stylesheets (SCSS).
- `routes/`: Application route definitions (`web.php`, `api.php`).
- `config/`: Configuration files.

## Installation & Setup

1. **Clone & Install**:
   ```bash
   composer install
   npm install
   ```

2. **Environment**:
   ```bash
   cp .env.example .env
   # Configure DB_*, PAYSTACK_*, FIREBASE_* credentials
   php artisan key:generate
   ```

3. **Run**:
   ```bash
   php artisan migrate
   php artisan serve
   npm run watch
   ```
