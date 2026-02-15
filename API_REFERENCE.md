# Peraly API Reference (Future)

This document outlines the planned API structure for Peraly. These endpoints are not currently implemented but are designed for future mobile app and third-party integration support.

## Planned REST API Endpoints

### Authentication

```
POST   /api/auth/login              - User login
POST   /api/auth/logout             - User logout
POST   /api/auth/refresh            - Refresh authentication token
GET    /api/auth/me                 - Get current user
```

### Transactions

```
GET    /api/transactions            - List all transactions
POST   /api/transactions            - Create transaction
GET    /api/transactions/{id}       - Get specific transaction
PUT    /api/transactions/{id}       - Update transaction
DELETE /api/transactions/{id}       - Delete transaction

Query Parameters:
  - date_from: YYYY-MM-DD
  - date_to: YYYY-MM-DD
  - type: cash-in|cash-out
  - category_id: integer
  - sort: created_at|transaction_date|amount
  - order: asc|desc
  - limit: integer (default: 50, max: 500)
  - offset: integer
```

### Categories

```
GET    /api/categories              - List all categories
POST   /api/categories              - Create category
GET    /api/categories/{id}         - Get specific category
PUT    /api/categories/{id}         - Update category
DELETE /api/categories/{id}         - Delete category

Query Parameters:
  - type: cash-in|cash-out
```

### Reports

```
GET    /api/reports                 - List all reports
POST   /api/reports                 - Generate new report
GET    /api/reports/{id}            - Get specific report
DELETE /api/reports/{id}            - Delete report

Query Parameters:
  - period: daily|weekly|monthly
  - start_date: YYYY-MM-DD
  - end_date: YYYY-MM-DD
```

### Analytics

```
GET    /api/analytics/summary       - Get financial summary
GET    /api/analytics/cashflow      - Get cash flow data
GET    /api/analytics/categories    - Get spending by category
```

### User

```
GET    /api/user/profile            - Get user profile
PUT    /api/user/profile            - Update user profile
POST   /api/user/password           - Change password
```

## Request/Response Format

### Headers
```
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

### Response Format

#### Success Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transaction_date": "2024-02-15",
    "type": "cash-in",
    "amount": 10000.00,
    "fee": 150.00,
    "category_id": 1,
    "notes": "Sample transaction",
    "created_at": "2024-02-15T10:30:00Z",
    "updated_at": "2024-02-15T10:30:00Z"
  },
  "message": "Transaction created successfully"
}
```

#### Error Response
```json
{
  "success": false,
  "error": "Validation failed",
  "errors": {
    "amount": ["Amount is required"],
    "type": ["Type must be cash-in or cash-out"]
  },
  "status_code": 422
}
```

### Pagination
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 50,
    "current_page": 1,
    "last_page": 2,
    "from": 1,
    "to": 50,
    "next_page_url": "http://api.example.com/transactions?page=2",
    "prev_page_url": null
  }
}
```

## Example Requests

### Create Transaction
```bash
curl -X POST http://localhost:8000/api/transactions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "transaction_date": "2024-02-15",
    "type": "cash-in",
    "amount": 10000,
    "category_id": 1,
    "notes": "Weekly sales"
  }'
```

### List Transactions with Filters
```bash
curl "http://localhost:8000/api/transactions?type=cash-in&date_from=2024-01-01&date_to=2024-02-15&limit=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Generate Report
```bash
curl -X POST http://localhost:8000/api/reports \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "period": "monthly",
    "start_date": "2024-02-01",
    "end_date": "2024-02-28"
  }'
```

## Authentication

### Login & Get Token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@peraly.com",
    "password": "password"
  }'

# Response:
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "expires_in": 3600,
  "token_type": "Bearer"
}
```

### Use Token
All subsequent requests must include:
```
Authorization: Bearer your_jwt_token_here
```

## Rate Limiting (Planned)

- **Unauthenticated**: 60 requests per minute per IP
- **Authenticated**: 1000 requests per minute per user
- **Headers**: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset

## Webhooks (Planned)

Webhooks allow external systems to be notified of events:

```
transaction.created
transaction.updated
transaction.deleted
report.generated
user.created
```

### Register Webhook
```bash
curl -X POST http://localhost:8000/api/webhooks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "url": "https://example.com/webhook",
    "events": ["transaction.created", "transaction.deleted"]
  }'
```

### Webhook Payload
```json
{
  "event": "transaction.created",
  "timestamp": "2024-02-15T10:30:00Z",
  "data": {
    "id": 1,
    "transaction_date": "2024-02-15",
    "type": "cash-in",
    "amount": 10000.00,
    "fee": 150.00,
    "category_id": 1,
    "notes": "Sample transaction"
  },
  "signature": "sha256_hmac_of_payload"
}
```

## SDK Examples

### JavaScript/Node.js (Planned)
```javascript
const Peraly = require('peraly-sdk');

const client = new Peraly({
  baseUrl: 'http://localhost:8000',
  token: 'your_token'
});

// Get all transactions
const transactions = await client.transactions.list({
  type: 'cash-in',
  dateFrom: '2024-01-01',
  dateTo: '2024-02-28'
});

// Create transaction
const transaction = await client.transactions.create({
  transactionDate: '2024-02-15',
  type: 'cash-in',
  amount: 10000,
  categoryId: 1,
  notes: 'Weekly sales'
});
```

### Python (Planned)
```python
from peraly import PeralyClient

client = PeralyClient(
    base_url='http://localhost:8000',
    token='your_token'
)

# Get transactions
transactions = client.transactions.list(
    type='cash-in',
    date_from='2024-01-01',
    date_to='2024-02-28'
)

# Create transaction
transaction = client.transactions.create(
    transaction_date='2024-02-15',
    type='cash-in',
    amount=10000,
    category_id=1,
    notes='Weekly sales'
)
```

## Error Codes

```
200  OK                    - Request successful
201  Created               - Resource created
400  Bad Request           - Invalid parameters
401  Unauthorized          - Missing or invalid token
403  Forbidden             - Not allowed to access resource
404  Not Found             - Resource not found
422  Unprocessable Entity  - Validation failed
429  Too Many Requests     - Rate limit exceeded
500  Internal Server Error - Server error
```

## Versioning

Future API versions will use URL versioning:

```
/api/v1/transactions
/api/v2/transactions
```

## Status Page

Check API status at: `/api/health`

```json
{
  "status": "online",
  "timestamp": "2024-02-15T10:30:00Z",
  "components": {
    "database": "online",
    "cache": "online",
    "queue": "online"
  }
}
```

## Implementation Timeline

- **Phase 1** (v1.1): Basic CRUD API for transactions and categories
- **Phase 2** (v1.2): Analytics and reporting endpoints
- **Phase 3** (v1.3): Webhook support
- **Phase 4** (v2.0): GraphQL layer
- **Phase 5** (v2.1): Mobile app (iOS/Android)

## Developer Notes

Once implemented, the API will be documented in OpenAPI/Swagger format at: `/api/docs`

Interactive API testing available at: `/api/playground`

---

For current implementation details, see [README.md](README.md) and source code in `app/Http/Controllers/Api/` (when implemented).
