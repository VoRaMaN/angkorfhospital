# Setup Project

Run the following commands to set up the project:

```bash
composer setup
```

On Unix-like systems (Linux/Mac):

```bash
./scripts/refresh_everything.sh
```

On Windows:

```bash
./scripts/refresh_everything.bat
```

## Features

This is a comprehensive clinic management system built with Laravel 12, Inertia.js v2, Vue 3, and Tailwind CSS v4. The application provides end-to-end healthcare facility management capabilities.

### Core Healthcare Management

#### Patient Management

- Complete patient registration and profile management
- Patient medical history tracking
- Patient file uploads and document management
- Patient report generation (PDF)
- Patient sticker generation for identification
- Medical record creation and management

#### Appointment System

- Appointment scheduling and management
- Calendar view for appointments
- Multiple appointment statuses: Scheduled, Confirmed, Arrived, In Progress, Completed, Cancelled, No Show, Rescheduled
- Appointment report and letter generation
- Status tracking and updates

#### Visit Management

- Patient visit tracking
- Visit assignment to medical staff
- Doctor's personal visit queue (my-visits, my-to-be-process-visits)
- Staff notification system for visits
- Visit status management

#### Medical Orders

- Comprehensive medical order system supporting multiple types:
  - Lab Orders
  - Procedures
  - Referrals
  - Therapy
  - Imaging
  - Consultation
- Order workflow: Creation → Processing → Completion
- Order status tracking (Pending, Processing, Completed, Cancelled)
- Priority levels (Routine, Urgent, STAT)
- Item-level completion tracking
- Cost breakdown and billing integration
- Medical order report generation
- Send back and cancellation capabilities

#### Medical Services

- Medical service catalog management
- Service pricing and descriptions
- Integration with billing system

#### Billing & Payments

- Billing management and invoice generation
- Billing status tracking (Pending, Partially Paid, Paid, Overdue, Cancelled)
- Billing report and letter generation
- Integration with medical orders for automatic billing

### Clinical Operations

#### Laboratory Management

- Lab panel configuration
- Lab inventory tracking
- Lab test ordering through medical orders
- Lab results management

#### Inventory Management

- Medical supply tracking
- Prescription medicine (Rx) inventory
- Lab inventory management
- Supply type categorization (Medicine, Equipment, Supplies, Consumables)

#### Medical Records

- Comprehensive medical record system
- Record report generation
- Integration with visits and medical orders

### Staff & Administration

#### Staff Management

- Staff registration and profile management
- Department assignments
- Staff file uploads and document management
- Role-based access control

#### Department Management

- Department creation and organization
- Staff assignment to departments

#### Doctor Features

- Personal appointment view
- Personal patient list
- Visit queue management
- Medical order creation and management

#### Role & Permission Management

- Role creation and management
- Permission assignment using Spatie Laravel Permission
- User role assignment

### User Management & Security

#### Authentication (Laravel Fortify)

- User registration
- Login/Logout
- Email verification
- Password reset
- Two-factor authentication (2FA) with QR code
  - Password confirmation required for enabling 2FA
  - OTP confirmation before activation

#### User Settings

- Profile management
- Password change
- Two-factor authentication setup
- Appearance settings (theme customization)
- User management (admin)

### Technical Features

#### Document Management

- File upload and storage system
- Patient-specific file management
- Staff-specific file management
- File download capabilities
- PDF report generation using DomPDF

#### Dashboard

- Centralized dashboard with key metrics
- Role-based dashboard views

#### Developer Tools

- Laravel Boost MCP server integration
- Laravel Pint for code formatting
- Pest v4 for testing (including browser tests)
- Laravel Pail for log viewing
- Queue management
- Database seeding and factories

### Technology Stack

**Backend:**

- PHP 8.3+
- Laravel 12
- Laravel Fortify (Authentication)
- Laravel Wayfinder (Type-safe routing)
- Spatie Laravel Permission (Roles & Permissions)
- DomPDF (PDF Generation)

**Frontend:**

- Vue 3
- Inertia.js v2
- Tailwind CSS v4
- TypeScript
- Vite

**Development:**

- Pest v4 (Testing)
- Laravel Pint (Code Formatting)
- Laravel Sail (Docker Development Environment)
- ESLint & Prettier
