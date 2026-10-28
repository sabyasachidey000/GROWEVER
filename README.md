# Manufacturing Execution System (MES)

A complete Enterprise Manufacturing Execution System for metal manufacturing companies built with Laravel 10 and PostgreSQL.

## Features

### Core Modules
- **User Management** - Role-based access control (7 roles)
- **Employee Management** - RFID assignment, department mapping
- **Machine Management** - CNC/Manual machines, status tracking, utilization
- **Operator Login by RFID** - Card swipe-based operator authentication
- **Shift Management** - 3-shift scheduling (A/B/C)
- **Production Tracking** - Cycle counts, quantities, approvals
- **Breakdown Management** - 8 breakdown types, maintenance workflow
- **Notification System** - Email, in-app, real-time alerts via WebSockets
- **Product Management** - Ferrous waste, flanges, bolts, fasteners
- **Rejection Management** - 9 rejection reasons with analysis
- **Supervisor Validation** - Approval/rejection workflow with audit trail
- **Quality Inspection** - First piece, in-process, final, patrol, receiving
- **Real-time Dashboard** - Charts, KPIs, machine status
- **OEE Calculation** - Availability × Performance × Quality
- **Reports** - 8 report types with PDF/Excel export
- **REST APIs** - Complete API for all modules

### Technology Stack
- **Backend:** Laravel 10, PHP 8.1, PostgreSQL
- **Auth:** Laravel Sanctum, Spatie Permission
- **Frontend:** Bootstrap 5, jQuery, AJAX, DataTables, Chart.js
- **Real-time:** Laravel WebSockets, Pusher
- **Reports:** DomPDF, PHPSpreadsheet
- **IoT:** MQTT Integration ready

## Installation

### Requirements
- PHP >= 8.1
- PostgreSQL >= 14
- Composer
- Node.js >= 18

### Setup

```bash
cd c:\wamp64\www\SampleNodeProject
composer install
npm install && npm run build

# Configure environment - update DB credentials in .env
# Set INITIAL_ADMIN_PASSWORD in .env to a unique password for the seeded administrator
php artisan key:generate
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### Initial Administrator

The database seeder creates `admin@mes.com`. Set `INITIAL_ADMIN_PASSWORD` in `.env`
to a unique password before running `php artisan db:seed`.

## Roles & Permissions

| Role | Key Permissions |
|------|----------------|
| Super Admin | Full access |
| Plant Manager | Dashboard, reports, approvals, all viewing |
| Production Manager | Production, breakdowns, OEE, reports |
| Maintenance Manager | Machines, breakdowns, reports |
| Shift Supervisor | Production, quality, approvals |
| Operator | Production entry, breakdowns, rejections |
| Quality Inspector | Quality inspections, rejections, reports |

## Architecture

### Service Layer
- `RfidService` - RFID card processing and operator sessions
- `DashboardService` - Real-time dashboard statistics
- `OeeService` - OEE calculation and history
- `NotificationService` - Multi-channel notifications
- `ReportService` - Report generation (PDF/Excel)
- `ActivityLogService` - Audit trail logging

### Event-Driven Design
Events and listeners handle machine breakdown notifications, OEE calculation on approval, production target alerts, and quality alerts.

## API

Complete REST API documented in [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## Deployment

See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for Ubuntu deployment instructions.

## Database Schema

18 tables: `users`, `employees`, `departments`, `designations`, `machines`, `products`, `shifts`, `shift_assignments`, `operator_sessions`, `production_runs`, `machine_breakdowns`, `rejections`, `quality_inspections`, `production_approvals`, `oee_records`, `notifications`, `activity_logs`, `rfid_logs`

## RFID Integration

Operator swipes RFID card at machine terminal, system validates employee and creates session. API: `POST /api/rfid/swipe`

## MQTT/IoT Integration

Configure MQTT in `.env`:
```
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
```

Supports PLC data collection, real-time monitoring, automated breakdown detection, cycle count capture.

## Security

- CSRF protection, Bcrypt password hashing
- Role-based authorization (Spatie Permission)
- Activity logging for audit trails
- Locked records after supervisor approval

## License

Proprietary - All rights reserved.
