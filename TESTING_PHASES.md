# Dooeats Testing Phases

## 1. Unit Testing
- **Scope**: Individual functions, classes, and components.
- **Tools**: PHPUnit.
- **Responsibility**: Developers.
- **Criteria**: All tests must pass before committing code.

## 2. Integration Testing
- **Scope**: Interaction between modules (e.g., Payment Gateway + Order System, Webhook + Notification).
- **Tools**: PHPUnit, Postman.
- **Responsibility**: Developers / QA.
- **Criteria**: Seamless data flow and error handling between systems.

## 3. User Acceptance Testing (UAT)
- **Scope**: End-to-end business flows (e.g., placing an order, restaurant accepting order, driver delivery).
- **Environment**: Staging Server (Mirror of Production).
- **Responsibility**: Product Owner / Beta Testers.
- **Criteria**: Verification of business requirements and user experience.

## 4. Production Verification
- **Scope**: Live environment sanity checks.
- **Responsibility**: DevOps / Lead Developer.
- **Criteria**: Critical paths functioning correctly after deployment (Smoke Testing).

## Test Data Management
- Use factory seeders for consistent test data.
- **Never** use real customer data in non-production environments.
