# MES API Documentation

## Base URL
```
http://localhost/api
```

## Authentication
All API routes (except `/api/auth/login`) require a Bearer token obtained from the login endpoint.

```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### POST /api/auth/login
Login and receive an API token.

**Request Body:**
```json
{
  "email": "admin@mes.com",
  "password": "<your configured password>"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "System Administrator",
    "email": "admin@mes.com",
    "employee": { ... }
  },
  "token": "1|abc123...",
  "roles": ["Super Admin"],
  "permissions": ["view users", "create users", ...]
}
```

### POST /api/auth/logout
Logout and invalidate current token.

### GET /api/auth/user
Get authenticated user details.

---

## Employee Endpoints

### GET /api/employees
List employees with pagination and filtering.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| department_id | int | Filter by department |
| status | string | Filter by status (active/inactive/terminated) |
| search | string | Search by name or employee code |
| page | int | Page number |

### GET /api/employees/{id}
Get single employee details.

---

## Machine Endpoints

### GET /api/machines
List all active machines.

### GET /api/machines/{id}
Get machine details.

### GET /api/machines/{id}/status
Get real-time machine status including current operator and active breakdowns.

---

## Product Endpoints

### GET /api/products
List all active products.

### GET /api/products/{id}
Get product details.

---

## Production Endpoints

### GET /api/production
List production records.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| date | string | Filter by date (YYYY-MM-DD) |
| machine_id | int | Filter by machine |

### POST /api/production
Create a new production entry.

**Request Body:**
```json
{
  "machine_id": 1,
  "operator_id": 1,
  "product_id": 1,
  "shift_id": 1,
  "start_time": "2024-01-01 06:00:00",
  "cycle_count": 100,
  "production_quantity": 500,
  "rejection_quantity": 5,
  "target_quantity": 480,
  "status": "completed"
}
```

### GET /api/production/{id}
Get production run details.

---

## Breakdown Endpoints

### GET /api/breakdowns
List breakdown records.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status (open/in_progress/resolved/closed) |
| machine_id | int | Filter by machine |

### POST /api/breakdowns
Report a new machine breakdown.

**Request Body:**
```json
{
  "machine_id": 1,
  "reported_by": 1,
  "breakdown_type": "mechanical",
  "severity": "high",
  "description": "Motor overheating"
}
```

### GET /api/breakdowns/{id}
Get breakdown details.

---

## Approval Endpoints

### GET /api/approvals/pending
List all pending production approvals.

### POST /api/approvals/{id}/approve
Approve a production record.

**Request Body:**
```json
{
  "comments": "Looks good"
}
```

### POST /api/approvals/{id}/reject
Reject a production record.

**Request Body:**
```json
{
  "reason": "Quantity mismatch with physical count"
}
```

---

## RFID Endpoints

### POST /api/rfid/punch
Record a successful ESP32 + RC522 attendance punch. The UID must be assigned to an **active** employee through `employees.rfid_card_number`.

**Request Body:**
```json
{
  "device_id": "esp32-gate-01",
  "uid": "A1B2C3D4"
}
```

**Successful response (201):**
```json
{
  "success": true,
  "message": "RFID punch recorded successfully.",
  "punch": {
    "id": 25,
    "punch_time": "2026-08-25T10:30:00+05:30",
    "device_id": "esp32-gate-01",
    "uid": "A1B2C3D4"
  },
  "person": {
    "id": 7,
    "employee_code": "EMP-007",
    "name": "John Doe",
    "department": "Production",
    "designation": "Operator"
  }
}
```

An unknown, unassigned, or inactive card returns `422` and does not create a successful punch log.

Successful records are available at the authenticated `GET /api/rfid/punch-logs` endpoint and in **RFID Punch Logs** in the web application.

### POST /api/rfid/button
Create an open, high-severity breakdown request when the ESP32 emergency/breakdown button is pressed. The endpoint uses the active RFID operator on the machine as the reporter. If the device is not already mapped, send `machine_id`; if no operator is logged in, also send `reported_by`.

**Request Body:**
```json
{
  "device_id": "esp32-gate-01",
  "machine_id": 1,
  "reported_by": 7,
  "description": "Operator pressed the emergency breakdown button."
}
```

Repeated button presses while a request is open return that existing breakdown instead of creating a duplicate.

### POST /api/rfid/swipe
Process an RFID card swipe.

**Request Body:**
```json
{
  "rfid_card_number": "RFID123456",
  "machine_id": 1,
  "action": "swipe_in",
  "reader_id": "reader_01"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Welcome, John Doe",
  "action": "swipe_in",
  "data": {
    "employee": { "id": 1, "name": "John Doe", ... },
    "machine": { "id": 1, "name": "CNC-001", ... },
    "shift": { "id": 1, "name": "Shift A", ... },
    "session": { "id": 1, "login_time": "..." }
  }
}
```

---

## Report Endpoints

### GET /api/reports/daily-production
Get daily production report.

### GET /api/reports/machine-utilization
Get machine utilization report.

### GET /api/reports/breakdown
Get breakdown report.

### GET /api/reports/operator-performance
Get operator performance report.

### GET /api/reports/rejection-analysis
Get rejection analysis report.

### GET /api/reports/oee
Get OEE report.

---

## OEE Endpoints

### GET /api/oee/summary
Get OEE summary for a date range.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| start_date | string | Start date (YYYY-MM-DD) |
| end_date | string | End date (YYYY-MM-DD) |

### GET /api/oee/ranking
Get machine OEE ranking for a specific date.

---

## Error Responses

All API errors follow this format:

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

**HTTP Status Codes:**
| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |
