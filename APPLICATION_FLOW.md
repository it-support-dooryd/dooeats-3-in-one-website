# Dooeats Application Flow & Architecture Documentation

**Generated Date:** December 28, 2025
**System Architecture:** Hybrid (Laravel Backend + Firebase Firestore)

## 1. Architecture Overview

 The Dooeats platform runs on a **Hybrid Architecture** where the Laravel PHP MVC framework handles the Web Interface (Views), Session Management, and Payment Gateway orchestration, while **Google Firebase Firestore** acts as the primary database for Order Management, Real-time Tracking, and Status Updates.

*   **Logic Core:** Distributed between Frontend JavaScript (Web) / Dart (Mobile) and Backend PHP.
*   **Data Persistence (Orders):** **Firebase Firestore** (Primary Source of Truth for active orders).
*   **Data Persistence (Users/Auth):** Hybrid. MySQL (Laravel `users` table) is used for Web Auth; Firebase Auth is used for Mobile/Web access to Firestore.
*   **Payment Processing:** Laravel Backend (server-side key management).

---

## 2. Customer Journey

### Step 1: Discovery & Cart
*   **Platform:** Web (Blade Views) or Mobile App.
*   **Data Source:** Firestore (Restaurants, Products, Categories).
*   **Action:** User adds items to Cart.
    *   *Web:* stored in PHP `Session`.
    *   *Mobile:* stored in Local State/SQLite.
*   **Logic:** Price calculation (tax, discount) happens on the client-side (or PHP for Web) before Checkout.

### Step 2: Checkout & Payment
*   **Action:** User proceeds to Checkout.
*   **Web Flow:**
    1.  Frontend constructs a complete `order_json` object.
    2.  Frontend POSTs this object to `/order-proccessing` (Laravel) to save it in the Session (to survive redirects).
    3.  User is redirected to Payment Gateway (Stripe, Razorpay, etc.) managed by `CheckoutController`.
    4.  **Success:** Gateway callbacks to `/success`.
*   **Mobile Flow:**
    1.  App constructs `order_json`.
    2.  App handles payment via Native SDKs or WebView.

### Step 3: Order Placement (Critical)
*   **Action:** Payment is confirmed.
*   **Data Move:**
    *   The post-payment page (`success.blade.php` on Web) initializes the Firebase SDK.
    *   It reads the `order_json` from the Session.
    *   **JavaScript writes the Order directly to Firestore** in the `restaurant_orders` collection.
    *   *Note:* The Laravel Backend does **NOT** write the order to MySQL or Firestore itself. The Client Browser does it.

### Step 4: Notification
*   **Action:** Client triggers `POST /order-complete`.
*   **Backend Role:** `ProductController@orderComplete` receives the request and sends an FCM (Firebase Cloud Messaging) notification to the Vendor/Admin.

---

## 3. Vendor Workflow

### Step 1: Monitoring
*   **Platform:** Restaurant Web Panel (`http://10.61.26.217:8002`).
*   **Data Move:**
    *   The Dashboard (`orders/index.blade.php`) initiates a **Firestore Listener** (`database.collection('restaurant_orders')`).
    *   It listens for new documents where `vendorID` matches the logged-in user.
    *   New orders appear in Real-time (No page refresh needed).

### Step 2: Order Acceptance
*   **Action:** Vendor clicks "Accept".
*   **Data Move:**
    *   JavaScript updates the specific Firestore document (`restaurant_orders/{id}`).
    *   Sets `status` parameter to `"Order Accepted"`.

### Step 3: Processing
*   **Status Updates:** Vendor updates status to `"Preparing"`, `"Ready"`.
*   **Data Move:** Each action performs a Direct Write to Firestore from the Browser.

---

## 4. Driver Workflow

### Step 1: Assignment
*   **Trigger:** Order status becomes `"Ready"` (or "Driver Pending" depending on configuration).
*   **Platform:** Driver Mobile App.
*   **Data Move:**
    *   Driver App listens to `restaurant_orders` (filtered by location or assignment).
    *   OR Admin assigns driver via Admin Panel (updating the Firestore document with `driverID`).

### Step 2: Delivery
*   **Action:** Driver accepts/picks up order.
*   **Data Move:** App updates Firestore status to `"In Transit"`.
*   **Real-time Tracking:** Driver App continuously updates their geo-location in Firestore (likely `users/{driverId}` or a separate `driver_locations` collection).

### Step 3: Completion
*   **Action:** Delivery confirmed.
*   **Data Move:** App updates Firestore status to `"Order Completed"`.

---

## 5. Admin Control Flow

*   **Platform:** Admin Panel (`http://10.61.26.217:8001`).
*   **Capabilities:**
    *   Admin views all orders via Firestore listeners.
    *   Admin can manually update status or delete orders (Firestore Delete).
    *   Admin manages Users/Vendors in MySQL (synced to Firestore via manual registration flows or listeners).

---

## 6. Data Schema (Firestore)

Based on the application logic, the `restaurant_orders` collection document structure is as follows:

```json
{
  "id": "UUID",
  "createdAt": "Timestamp",
  "status": "Order Placed" | "Order Accepted" | "In Transit" | "Order Completed",
  "author": {
    "id": "User UUID",
    "firstName": "String",
    "lastName": "String",
    "email": "String",
    "phone": "String",
    "shippingAddress": "Object"
  },
  "vendor": {
    "id": "Vendor UUID",
    "title": "String",
    "address": "String"
  },
  "products": [
    {
      "id": "Product UUID",
      "name": "String",
      "price": "Number",
      "quantity": "Number",
      "image": "URL",
      "extras": [],
      "size": "String"
    }
  ],
  "address": {
      "address": "String",
      "location": { "latitude": Float, "longitude": Float }
  },
  "payment_method": "stripe" | "cod" | "wallet",
  "deliveryCharge": "Number",
  "tip_amount": "Number",
  "tax": "Number",
  "discount": "Number",
  "total": "Number" (Calculated implicitly or stored)
}
```

## 7. Integration Notes for Mobile Apps

1.  **Base URLs:**
    *   **Customer App:** `http://10.61.26.217:8000` (Use for WebView Checkout if native checkout is not implemented).
    *   **Admin App:** `http://10.61.26.217:8001`
    *   **Restaurant App:** `http://10.61.26.217:8002`

2.  **API Usage:**
    *   The Mobile App should **NOT** rely on REST APIs for creating orders.
    *   It must implement the **Firebase Firestore SDK** to write to the `restaurant_orders` collection directly, matching the schema above.
    *   The `/api/delete-user` endpoint is available for account deletion.

3.  **Synchronization:**
    *   Ensure the JSON structure generated by the Mobile App matches exactly what `CheckoutController` (PHP) and `success.blade.php` (JS) expect/produce to avoid display issues in the Admin/Vendor panels.
