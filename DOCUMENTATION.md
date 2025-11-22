# Dooeats Platform Documentation

## System Architecture
The Dooeats platform consists of three main applications sharing a common database:
1. **Customer App**: For end-users to browse restaurants and place orders.
2. **Restaurant App**: For restaurant partners to manage menus and orders.
3. **Admin App**: For platform administrators to oversee operations.

## Key Modules

### User Management
- **Customers**: Registration via Email/Phone or Social Login.
- **Restaurants**: Partner onboarding and verification.
- **Drivers**: Delivery personnel management.

### Order Processing
1. **Placement**: Customer selects items and checks out.
2. **Acceptance**: Restaurant accepts the order.
3. **Preparation**: Restaurant prepares food.
4. **Assignment**: Driver is assigned (Manual/Auto).
5. **Delivery**: Driver picks up and delivers.
6. **Completion**: Order marked as delivered.

### Payments
- Integrated Gateways: Stripe, PayPal, Razorpay, Paystack (Coming Soon).
- Wallet System: Users can top up wallets for quick payments.

### Notifications
- **Push Notifications**: Firebase Cloud Messaging (FCM).
- **Email**: SMTP / Mailgun.
- **SMS**: Twilio / Termii (Coming Soon).

## API Documentation
*Link to Swagger/Postman documentation if available.*

## Database Schema
*Brief overview of key tables:*
- `users`: Customer data.
- `restaurants`: Restaurant profiles.
- `orders`: Order details.
- `foods`: Menu items.
