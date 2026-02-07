# Extendable Order & Payment Management API

A Laravel 12 REST API for order and payment management with **extensible payment gateway architecture** using the Strategy Pattern.

## 🏗️ Architecture

```
app/
├── Contracts/           # Interfaces
├── DTOs/               # Data Transfer Objects
├── Enums/              # OrderStatus, PaymentStatus
├── Exceptions/         # Business exceptions
├── Http/Controllers/Api/V1/
├── Models/
├── Policies/           # Authorization
├── Repositories/       # Data access layer
├── Services/           # Business logic
│   └── Payment/Gateways/  # Strategy Pattern
├── Support/            # Helpers (ApiResponse, TestCards)
└── Traits/
```

### Design Patterns

| Pattern | Usage |
|---------|-------|
| **Repository** | Orders, Payments - Testable data access |
| **Service Layer** | Business logic separation |
| **Strategy** | Payment Gateways - Easy extensibility |
| **DTO** | PaymentResult - Type-safe responses |

---

## 🚀 Setup

```bash
# Clone and install
composer install
cp .env.example .env
php artisan key:generate

# JWT
php artisan jwt:secret

# Database
php artisan migrate

# Run
php artisan serve
```

---

## 📬 Postman Collection

Import the collection from `postman/Order.postman_collection.json` into Postman.

**Setup:**

1. Import the collection
2. Set `base_url` variable to `http://localhost:8000/api/v1`
3. After login, set `token` variable with JWT

---

## 📡 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register user |
| POST | `/auth/login` | Login, returns JWT |
| POST | `/auth/logout` | Logout (protected) |
| GET | `/auth/me` | Current user (protected) |

### Orders (Protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/orders` | List orders (`?status=pending`) |
| POST | `/orders` | Create order with items |
| GET | `/orders/{id}` | Show order |
| PUT | `/orders/{id}` | Update order/items |
| DELETE | `/orders/{id}` | Delete (if no payments) |
| PATCH | `/orders/{id}/status` | Update status |

### Payments (Protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/orders/{id}/payments` | Process payment |
| GET | `/orders/{id}/payments` | Order's payments |
| GET | `/payments/methods` | Available gateways |

---

## 💳 Payment Gateways

### Available Methods

- `credit_card`
- `paypal`
- `stripe`

### Test Cards (Industry Standard)

| Card Number | Result |
|-------------|--------|
| `4242424242424242` | ✅ Success |
| `5555555555554444` | ✅ Success (Mastercard) |
| `4000000000000002` | ❌ Declined |
| `4000000000009995` | ❌ Insufficient funds |
| `4000000000000119` | ❌ Processing error |

### PayPal Test Emails

| Email | Result |
|-------|--------|
| `success@test.com` | ✅ Success |
| `declined@test.com` | ❌ Declined |

---

## 📝 Request Examples

### Create Order

```json
POST /api/v1/orders
{
  "items": [
    {"product_name": "Product A", "quantity": 2, "price": 29.99},
    {"product_name": "Product B", "quantity": 1, "price": 49.99}
  ]
}
```

### Process Payment (Credit Card)

```json
POST /api/v1/orders/1/payments
{
  "method": "credit_card",
  "card_number": "4242424242424242",
  "expiry_month": 12,
  "expiry_year": 2027,
  "cvv": "123"
}
```

### Process Payment (PayPal)

```json
POST /api/v1/orders/1/payments
{
  "method": "paypal",
  "email": "success@test.com"
}
```

---

## ➕ Adding New Payment Gateway

1. **Create Gateway Class**

```php
// app/Services/Payment/Gateways/NewGateway.php
class NewGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details = []): PaymentResult
    {
        // Implementation
    }

    public function getName(): string
    {
        return 'new_gateway';
    }

    public function isConfigured(): bool
    {
        return true;
    }
}
```

1. **Register in Config**

```php
// config/payment.php
'gateways' => [
    'new_gateway' => \App\Services\Payment\Gateways\NewGateway::class,
],
```

That's it! Gateway is automatically available.

---

## 🔒 Business Rules

- ❌ Cannot delete order with payments
- ❌ Cannot cancel order with successful payment
- ❌ Cannot modify cancelled orders

---

## 📊 Response Format

All responses follow consistent format:

```json
{
  "status": 200,
  "success": true,
  "message": "Orders retrieved successfully",
  "data": { ... }
}
```

Error response:

```json
{
  "status": 422,
  "success": false,
  "message": "Validation failed",
  "errors": { "field": ["Error message"] }
}
```

---

## 🛠️ Tech Stack

- **Laravel 12** - Framework
- **JWT Auth** - Authentication
- **MySQL/SQLite** - Database

---

## 📄 License

MIT
