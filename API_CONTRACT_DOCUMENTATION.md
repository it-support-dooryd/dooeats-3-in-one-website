# Dooeats API Contract Documentation

**Generated:** December 28, 2025  
**Base URLs:**
- Customer App: `http://10.61.26.217:8000`
- Admin App: `http://10.61.26.217:8001`
- Restaurant/Vendor App: `http://10.61.26.217:8002`

---

## Table of Contents
1. [Authentication](#authentication)
2. [Customer API Endpoints](#customer-api-endpoints)
3. [Vendor/Restaurant API Endpoints](#vendor-restaurant-api-endpoints)
4. [Driver API Endpoints](#driver-api-endpoints)
5. [Common Response Structures](#common-response-structures)
6. [Error Handling](#error-handling)

---

## Authentication

### Firebase Authentication
The application uses **Firebase Authentication** for user management. All three apps (Customer, Restaurant, Driver) authenticate through Firebase.

**Firebase Configuration:**
```
FIREBASE_APIKEY=AIzaSyAZ9xxKXmo9dWC0SO9UmxHoglP_ZGTQEUQ
FIREBASE_AUTH_DOMAIN=dooeats-c690f.firebaseapp.com
FIREBASE_DATABASE_URL=https://dooeats-c690f-default-rtdb.europe-west1.firebasedatabase.app
FIREBASE_PROJECT_ID=dooeats-c690f
FIREBASE_STORAGE_BUCKET=dooeats-c690f.firebasestorage.app
```

### Token Management

#### Set Token
**Endpoint:** `POST /setToken`  
**Purpose:** Store Firebase FCM token for push notifications

**Request:**
```json
{
  "token": "string (FCM token)"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Token stored successfully"
}
```

#### Logout
**Endpoint:** `POST /logout`  
**Purpose:** Clear user session and tokens

**Response:**
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

---

## Customer API Endpoints

### Base URL: `http://10.61.26.217:8000`

### 1. User Management

#### Delete User Account
**Endpoint:** `POST /api/delete-user`  
**Purpose:** Delete user account and all associated data

**Request:**
```json
{
  "uuid": "string (vendor_users.uuid)"
}
```

**Response (Success):**
```json
{
  "status": "okay",
  "message": "User and associated records deleted successfully."
}
```

**Response (Error):**
```json
{
  "status": "okay",
  "message": "UUID validation error message"
}
```

#### Register New User
**Endpoint:** `POST /newRegister`

**Request:**
```json
{
  "email": "string",
  "password": "string",
  "name": "string",
  "phone": "string"
}
```

#### Check Email Availability
**Endpoint:** `POST /checkEmail`

**Request:**
```json
{
  "email": "string"
}
```

### 2. Restaurant & Product Discovery

#### Home Page
**Endpoint:** `GET /`  
**Purpose:** Load homepage with featured restaurants, categories, banners

**Response Structure:**
- Featured restaurants
- Top categories
- Active banners
- Special offers

#### Search
**Endpoint:** `GET /search?q={query}`  
**Purpose:** Search for restaurants and products

**Query Parameters:**
- `q`: Search query string

#### All Restaurants
**Endpoint:** `GET /restaurants`  
**Purpose:** List all available restaurants

#### Restaurants by Category
**Endpoint:** `GET /restaurants/category/{id}`  
**Purpose:** Filter restaurants by category

**Path Parameters:**
- `id`: Category ID

#### Category List
**Endpoint:** `GET /categories`  
**Purpose:** Get all available categories

#### Category Detail
**Endpoint:** `GET /category/{id}`  
**Purpose:** Get specific category with restaurants

#### Restaurant Detail
**Endpoint:** `GET /restaurant?id={id}`  
**Purpose:** Get restaurant details, menu, and products

#### Product List
**Endpoint:** `GET /products/{type}/{id}`  
**Purpose:** Get products by type (restaurant/category)

**Path Parameters:**
- `type`: "restaurant" or "category"
- `id`: Restaurant or category ID

#### Product Detail
**Endpoint:** `GET /product/{id}`  
**Purpose:** Get detailed product information

**Path Parameters:**
- `id`: Product ID

### 3. Cart Management

#### View Cart
**Endpoint:** `GET /cart`  
**Purpose:** View current cart contents

#### Add to Cart
**Endpoint:** `POST /add-to-cart`

**Request:**
```json
{
  "product_id": "string",
  "quantity": "integer",
  "variant_info": "object (optional)",
  "addons": "array (optional)",
  "special_instructions": "string (optional)"
}
```

#### Update Cart
**Endpoint:** `POST /update-cart`

**Request:**
```json
{
  "cart_item_id": "string",
  "quantity": "integer"
}
```

#### Change Quantity
**Endpoint:** `POST /change-quantity-cart`

**Request:**
```json
{
  "cart_item_id": "string",
  "action": "increase|decrease"
}
```

#### Remove from Cart
**Endpoint:** `POST /remove-from-cart`

**Request:**
```json
{
  "cart_item_id": "string"
}
```

#### Add Cart Note
**Endpoint:** `POST /add-cart-note`

**Request:**
```json
{
  "note": "string"
}
```

#### Apply Coupon
**Endpoint:** `POST /apply-coupon`

**Request:**
```json
{
  "coupon_code": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "discount_amount": "float",
  "message": "Coupon applied successfully"
}
```

### 4. Checkout & Orders

#### Checkout Page
**Endpoint:** `GET /checkout`  
**Purpose:** Display checkout page with order summary

#### Set Delivery Option
**Endpoint:** `POST /order-delivery-option`

**Request:**
```json
{
  "delivery_option": "delivery|pickup|dinein"
}
```

#### Add Tip
**Endpoint:** `POST /order-tip-add`

**Request:**
```json
{
  "tip_amount": "float"
}
```

#### Schedule Order Time
**Endpoint:** `POST /order-schedule-time-add`

**Request:**
```json
{
  "schedule_time": "datetime (ISO 8601)"
}
```

#### Process Payment
**Endpoint:** `GET /pay`  
**Purpose:** Initiate payment process

#### Order Processing
**Endpoint:** `POST /order-proccessing`

**Request:**
```json
{
  "payment_method": "string",
  "delivery_address_id": "string",
  "payment_details": "object"
}
```

#### Complete Order
**Endpoint:** `POST /order-complete`

**Request:**
```json
{
  "order_id": "string",
  "transaction_id": "string",
  "payment_status": "success|failed"
}
```

#### Payment Success Callback
**Endpoint:** `GET /success`  
**Purpose:** Handle successful payment callback

#### Payment Failed Callback
**Endpoint:** `GET /failed`  
**Purpose:** Handle failed payment callback

#### Payment Notification
**Endpoint:** `GET /notify`  
**Purpose:** Handle payment gateway notifications

### 5. Order Management

#### My Orders
**Endpoint:** `GET /my_order`  
**Purpose:** List all user orders

#### Order Details
**Endpoint:** `GET /my_order/{id}`  
**Purpose:** Get specific order details

**Path Parameters:**
- `id`: Order ID

#### Completed Orders
**Endpoint:** `GET /completed_order`  
**Purpose:** List completed orders

#### Pending Orders
**Endpoint:** `GET /pending_order`  
**Purpose:** List pending orders

#### Cancelled Orders
**Endpoint:** `GET /cancelled_order`  
**Purpose:** List cancelled orders

#### Dine-in Orders
**Endpoint:** `GET /my_dinein`  
**Purpose:** List dine-in orders

#### Reorder
**Endpoint:** `POST /reorder-add-to-cart`

**Request:**
```json
{
  "order_id": "string"
}
```

### 6. Wallet & Transactions

#### Wallet Top-up
**Endpoint:** `GET /pay-wallet`  
**Purpose:** Initiate wallet top-up

#### Wallet Processing
**Endpoint:** `POST /wallet-proccessing`

**Request:**
```json
{
  "amount": "float",
  "payment_method": "string"
}
```

#### Wallet Success
**Endpoint:** `GET /wallet-success`  
**Purpose:** Handle successful wallet top-up

#### Wallet Notification
**Endpoint:** `GET /wallet-notify`  
**Purpose:** Handle wallet payment notifications

#### Transaction History
**Endpoint:** `GET /transactions`  
**Purpose:** View wallet transaction history

### 7. User Profile

#### View Profile
**Endpoint:** `GET /profile`  
**Purpose:** View user profile information

#### Delivery Addresses
**Endpoint:** `GET /delivery-address`  
**Purpose:** Manage delivery addresses

### 8. Favorites

#### Favorite Stores
**Endpoint:** `GET /favorite-stores`  
**Purpose:** List favorite restaurants

#### Favorite Products
**Endpoint:** `GET /favorite-products`  
**Purpose:** List favorite products

### 9. Gift Cards

#### Buy Gift Card
**Endpoint:** `GET /buy-gift-card`  
**Purpose:** Purchase gift card page

#### Gift Card Processing
**Endpoint:** `POST /gift-card-processing`

**Request:**
```json
{
  "amount": "float",
  "recipient_email": "string",
  "recipient_name": "string",
  "message": "string (optional)"
}
```

#### Pay for Gift Card
**Endpoint:** `GET /pay-giftcard`  
**Purpose:** Process gift card payment

#### Gift Card Success
**Endpoint:** `GET /gift-card-success`  
**Purpose:** Gift card purchase confirmation

#### My Gift Cards
**Endpoint:** `GET /giftcards`  
**Purpose:** View purchased gift cards

### 10. Other Features

#### Offers
**Endpoint:** `GET /offers`  
**Purpose:** View available offers and coupons

#### FAQ
**Endpoint:** `GET /faq`  
**Purpose:** Frequently asked questions

#### Contact Us
**Endpoint:** `GET /contact-us`  
**Purpose:** Contact form page

#### Send Contact Email
**Endpoint:** `POST /sendemail/send`

**Request:**
```json
{
  "name": "string",
  "email": "string",
  "subject": "string",
  "message": "string"
}
```

#### Trending
**Endpoint:** `GET /trending`  
**Purpose:** View trending restaurants/products

#### Privacy Policy
**Endpoint:** `GET /privacy`  
**Purpose:** View privacy policy

#### Terms of Service
**Endpoint:** `GET /terms`  
**Purpose:** View terms of service

#### Custom Page
**Endpoint:** `GET /page/{slug}`  
**Purpose:** View custom CMS page

---

## Vendor/Restaurant API Endpoints

### Base URL: `http://10.61.26.217:8002`

### 1. Authentication & Registration

#### Register
**Endpoint:** `GET /register`  
**Purpose:** Restaurant registration page

#### Signup
**Endpoint:** `GET /signup`  
**Purpose:** Restaurant signup form

#### Phone Registration
**Endpoint:** `GET /register/phone`  
**Purpose:** Phone-based registration

### 2. Subscription Management

#### View Subscription Plans
**Endpoint:** `GET /subscription-plan`  
**Purpose:** Display available subscription plans

#### Subscription Checkout
**Endpoint:** `GET /subscription-plan/checkout/{id}`  
**Purpose:** Checkout for subscription plan

**Path Parameters:**
- `id`: Subscription plan ID

#### Payment Processing
**Endpoint:** `POST /payment-proccessing`

**Request:**
```json
{
  "plan_id": "string",
  "payment_method": "string"
}
```

#### Pay Subscription
**Endpoint:** `GET /pay-subscription`  
**Purpose:** Process subscription payment

#### Complete Subscription Order
**Endpoint:** `POST /order-complete`

**Request:**
```json
{
  "subscription_id": "string",
  "transaction_id": "string"
}
```

#### My Subscriptions
**Endpoint:** `GET /my-subscriptions`  
**Purpose:** View active subscriptions

#### Subscription Details
**Endpoint:** `GET /my-subscription/show/{id}`  
**Purpose:** View specific subscription details

**Path Parameters:**
- `id`: Subscription ID

### 3. Dashboard & Analytics

#### Dashboard
**Endpoint:** `GET /` or `GET /dashboard`  
**Purpose:** Restaurant dashboard with analytics

**Response includes:**
- Total orders
- Revenue statistics
- Pending orders count
- Recent orders
- Popular products

### 4. Restaurant Management

#### Restaurant Profile
**Endpoint:** `GET /restaurant`  
**Purpose:** View/edit restaurant information

#### User Profile
**Endpoint:** `GET /users/profile`  
**Purpose:** View/edit user profile

### 5. Food/Product Management

#### List Foods
**Endpoint:** `GET /foods`  
**Purpose:** List all restaurant products

#### Create Food
**Endpoint:** `GET /foods/create`  
**Purpose:** Add new product form

#### Edit Food
**Endpoint:** `GET /foods/edit/{id}`  
**Purpose:** Edit product details

**Path Parameters:**
- `id`: Product ID

### 6. Order Management

#### All Orders
**Endpoint:** `GET /orders`  
**Purpose:** List all orders

#### Order Details
**Endpoint:** `GET /orders/edit/{id}`  
**Purpose:** View/manage specific order

**Path Parameters:**
- `id`: Order ID

#### Placed Orders
**Endpoint:** `GET /placedOrders`  
**Purpose:** List newly placed orders

#### Accepted Orders
**Endpoint:** `GET /acceptedOrders`  
**Purpose:** List accepted orders

#### Rejected Orders
**Endpoint:** `GET /rejectedOrders`  
**Purpose:** List rejected orders

#### Print Order
**Endpoint:** `GET /orders/print/{id}`  
**Purpose:** Print order receipt

**Path Parameters:**
- `id`: Order ID

#### Send Order Status Notification
**Endpoint:** `POST /order-status-notification`

**Request:**
```json
{
  "order_id": "string",
  "status": "string",
  "message": "string (optional)"
}
```

### 7. Coupon Management

#### List Coupons
**Endpoint:** `GET /coupons`  
**Purpose:** List all restaurant coupons

#### Create Coupon
**Endpoint:** `GET /coupons/create`  
**Purpose:** Create new coupon

#### Edit Coupon
**Endpoint:** `GET /coupons/edit/{id}`  
**Purpose:** Edit coupon details

**Path Parameters:**
- `id`: Coupon ID

### 8. Dine-in/Table Booking

#### Book Table Requests
**Endpoint:** `GET /booktable`  
**Purpose:** List table booking requests

#### Table Booking Details
**Endpoint:** `GET /booktable/edit/{id}`  
**Purpose:** View/manage booking request

**Path Parameters:**
- `id`: Booking ID

#### Send Booking Notification
**Endpoint:** `POST /sendnotification`

**Request:**
```json
{
  "booking_id": "string",
  "status": "accepted|rejected",
  "message": "string (optional)"
}
```

### 9. Payments & Earnings

#### Payments
**Endpoint:** `GET /payments`  
**Purpose:** View payment history

#### Create Payout Request
**Endpoint:** `GET /payments/create`  
**Purpose:** Request payout

#### Earnings
**Endpoint:** `GET /earnings`  
**Purpose:** View earnings summary

#### Wallet Transactions
**Endpoint:** `GET /wallettransaction`  
**Purpose:** View wallet transaction history

### 10. Documents

#### Document List
**Endpoint:** `GET /document-list`  
**Purpose:** View required documents

#### Upload Document
**Endpoint:** `GET /document/upload/{id}`  
**Purpose:** Upload verification document

**Path Parameters:**
- `id`: Document type ID

### 11. Withdraw Methods

#### Withdraw Methods
**Endpoint:** `GET /withdraw-method`  
**Purpose:** Manage withdrawal methods

#### Add Withdraw Method
**Endpoint:** `GET /withdraw-method/add`  
**Purpose:** Add new withdrawal method

### 12. Advertisements

#### List Advertisements
**Endpoint:** `GET /advertisements`  
**Purpose:** View restaurant advertisements

#### Pending Advertisements
**Endpoint:** `GET /advertisements/pending`  
**Purpose:** View pending ad requests

#### Create Advertisement
**Endpoint:** `GET /advertisements/create`  
**Purpose:** Create new advertisement

#### Edit Advertisement
**Endpoint:** `GET /advertisements/edit/{id}`  
**Purpose:** Edit advertisement

**Path Parameters:**
- `id`: Advertisement ID

#### View Advertisement
**Endpoint:** `GET /advertisements/view/{id}`  
**Purpose:** View advertisement details

#### Advertisement Chat
**Endpoint:** `GET /advertisement/chat/{id}`  
**Purpose:** Chat about advertisement

### 13. Delivery Personnel

#### List Deliverymen
**Endpoint:** `GET /deliveryman`  
**Purpose:** View restaurant's delivery personnel

#### Create Deliveryman
**Endpoint:** `GET /deliveryman/create`  
**Purpose:** Add new delivery person

#### Edit Deliveryman
**Endpoint:** `GET /deliveryman/edit/{id}`  
**Purpose:** Edit delivery person details

---

## Driver API Endpoints

### Base URL: `http://10.61.26.217:8001`

Driver-specific endpoints are managed through the admin panel. Drivers interact primarily through the mobile app with Firebase Realtime Database for real-time order updates.

### Key Driver Operations (via Admin):

#### List Drivers
**Endpoint:** `GET /drivers`  
**Purpose:** List all drivers

#### Approved Drivers
**Endpoint:** `GET /drivers/approved`  
**Purpose:** List approved drivers

#### Pending Drivers
**Endpoint:** `GET /drivers/pending`  
**Purpose:** List pending driver applications

#### Create Driver
**Endpoint:** `GET /drivers/create`  
**Purpose:** Add new driver

#### Edit Driver
**Endpoint:** `GET /drivers/edit/{id}`  
**Purpose:** Edit driver details

#### View Driver
**Endpoint:** `GET /drivers/view/{id}`  
**Purpose:** View driver profile

#### Driver Documents
**Endpoint:** `GET /drivers/document-list/{id}`  
**Purpose:** View driver documents

#### Upload Driver Document
**Endpoint:** `GET /drivers/document/upload/{driverId}/{id}`  
**Purpose:** Upload driver verification document

#### Driver Payments
**Endpoint:** `GET /driverpayments`  
**Purpose:** View driver payment history

#### Driver Payouts
**Endpoint:** `GET /driversPayouts`  
**Purpose:** Manage driver payouts

#### Create Driver Payout
**Endpoint:** `GET /driversPayouts/create`  
**Purpose:** Create payout for driver

---

## Common Response Structures

### Success Response
```json
{
  "status": "success",
  "data": {},
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### Pagination Response
```json
{
  "status": "success",
  "data": [],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}
```

---

## Error Handling

### HTTP Status Codes

- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

### Common Error Messages

**Validation Errors:**
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**Authentication Errors:**
```json
{
  "status": "error",
  "message": "Unauthenticated."
}
```

**Not Found Errors:**
```json
{
  "status": "error",
  "message": "Resource not found."
}
```

---

## Database Configuration

### Customer App
```
DB_DATABASE="Dooeats Customer Database"
DB_USERNAME="Dooeats Database User 1"
DB_PASSWORD=Dooeats2025
```

### Admin App
```
DB_DATABASE="Dooeats Admin Database"
DB_USERNAME="Dooeats Database User 2"
DB_PASSWORD=Dooeats2025
```

### Restaurant App
```
DB_DATABASE="Dooeats Restaurant Database"
DB_USERNAME="Dooeats Database User 1"
DB_PASSWORD=Dooeats2025
```

---

## Firebase Realtime Database Structure

The application uses Firebase Realtime Database for real-time features:

### Orders Node
```
/restaurant_orders/{orderId}
  - status: "pending|accepted|preparing|ready|picked_up|delivered"
  - customer_id: "string"
  - vendor_id: "string"
  - driver_id: "string"
  - items: []
  - total_amount: float
  - created_at: timestamp
  - updated_at: timestamp
```

### Driver Location
```
/driver_locations/{driverId}
  - latitude: float
  - longitude: float
  - is_available: boolean
  - current_order_id: "string"
  - updated_at: timestamp
```

---

## Payment Gateway Integration

### Supported Payment Methods
- **Paystack**: Configured in settings
- **Cash on Delivery (COD)**
- **Wallet Payment**

### Paystack Configuration
```
Settings > Payment Method > Paystack
```

---

## Notes for Mobile App Development

1. **Base URLs**: Use the network IP addresses provided above for local development
2. **Authentication**: Implement Firebase Authentication SDK
3. **Real-time Updates**: Use Firebase Realtime Database listeners for order status updates
4. **Push Notifications**: Implement FCM for push notifications
5. **Image URLs**: All image paths are relative to the base URL
6. **Session Management**: Use Laravel Sanctum tokens for API authentication
7. **CORS**: Ensure CORS headers are properly configured for API requests

---

## API Testing

### Recommended Tools
- Postman
- Insomnia
- cURL

### Sample cURL Request
```bash
curl -X POST http://10.61.26.217:8000/api/delete-user \
  -H "Content-Type: application/json" \
  -d '{"uuid": "example-uuid-here"}'
```

---

**Document Version:** 1.0  
**Last Updated:** December 28, 2025  
**Maintained By:** Dooeats Development Team
