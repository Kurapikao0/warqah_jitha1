# ورقة وجذع | Warqah Wa Jitha

<p align="center">
  <strong>Arabic E-Commerce Store for Handmade Products</strong>
</p>

<p align="center">
  A modern, API-first e-commerce store built for the Yemeni market.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Sanctum-4.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Sanctum">
  <img src="https://img.shields.io/badge/REST-API-02569B?style=for-the-badge" alt="REST API">
  <img src="https://img.shields.io/badge/PostgreSQL-Database-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
</p>

---

## About the Store

**ورقة وجذع (Warqah Wa Jitha)** is an Arabic e-commerce store focused on selling handmade products and products made from alternative natural materials, with a particular focus on the Yemeni market.

The store is designed to provide customers with a complete online shopping experience, from discovering products and managing favorites and the shopping cart to placing orders, submitting payments, tracking order progress, requesting customizations, and reviewing purchased products.

The backend is implemented as a **RESTful API** using Laravel 12 and is designed to be consumed by a separate frontend application such as React.

The project follows a structured layered architecture to keep presentation, validation, business logic, persistence, and API responses clearly separated.

---

## Project Vision

The goal of **Warqah Wa Jitha** is to transform the traditional process of selling handmade products into a modern digital shopping experience that is:

* Simple for customers.
* Flexible for customized products.
* Suitable for the Yemeni market.
* Easy to manage by store administrators.
* Maintainable for developers.
* Ready to integrate with a modern frontend.
* Structured for future growth.

---

# What Does the Store Provide?

The store covers the major parts of an e-commerce customer journey.

```text
                    WARQAH WA JITHA
                          │
          ┌───────────────┴───────────────┐
          │                               │
       CUSTOMER                         ADMIN
          │                               │
    ┌─────┴─────┐                   ┌─────┴─────┐
    │           │                   │           │
 Discover     Account             Catalog     Customers
    │           │                   │           │
 Products    Addresses            Orders      Payments
    │           │                   │           │
Favorites     Cart              Production    Reviews
    │           │                   │           │
Customize     Orders             Inventory    Settings
    │           │                   │           │
 Payment      Reviews            Media       Permissions
```

---

# Customer Experience

Customers can interact with the store through dedicated customer APIs.

### Authentication

* Customer registration
* Customer login
* Customer logout
* Password recovery
* Password reset
* Verification

### Profile

* View profile
* Update profile
* Change password
* Update avatar

### Shopping

* Browse products through the API
* Manage favorites
* Manage shopping cart
* Manage cart items
* Manage delivery addresses
* Select default address

### Customization

The store supports product customization workflows, allowing customers to submit and manage customization requests associated with products.

### Orders

Customers can:

* Create orders
* View their orders
* View individual order details
* Follow the order lifecycle

### Payments

Customers can:

* View payment information
* Submit payment information
* Access payment records associated with their orders

### Reviews

Customers can:

* Submit product reviews
* Manage their reviews
* Upload review images

### Notifications

Customers can access and manage their store notifications.

---

# Store Administration

The store includes a dedicated administration API for managing the e-commerce operation.

Administrators can manage:

### Product Catalog

* Products
* Product categories
* Product attributes
* Attribute values
* Product media
* Colors
* Design patterns
* Raw materials

### Product Media

The API supports dedicated media operations including:

* Uploading product media
* Reordering product media
* Selecting a primary product image
* Managing product media records

### Customers

Administrators can:

* View customers
* Manage customer records
* Change customer status
* Verify customers
* Restore customers where applicable

### Orders

Administrators can:

* View orders
* Manage orders
* Update order status
* View order statistics
* View status history
* View production history
* Move orders between production stages

### Payments

Administrators can:

* View payments
* View payment details
* Update payment status

### Reviews

Administrators can:

* Review customer reviews
* Moderate review status
* Reply to reviews
* Remove reviews

### Store Management

The administration API also provides management for:

* Admin users
* Roles
* Permissions
* Role permissions
* System settings
* Exchange rates
* Notifications
* Activity logs

---

# Product Customization

One of the important features of **Warqah Wa Jitha** is support for customized products.

The customization workflow is designed around the store's product catalog and allows customers to submit customization requests while giving administrators tools to review and manage them.

Conceptually:

```text
Customer
   │
   ▼
Select Product
   │
   ▼
Customization Request
   │
   ▼
Store Processing
   │
   ▼
Order / Production
   │
   ▼
Customer
```

This allows the store to support products that cannot be handled as simple fixed catalog items.

---

# Order & Production Workflow

The store separates the customer's order lifecycle from internal production management.

```text
Customer Order
      │
      ▼
Order Management
      │
      ▼
Order Status
      │
      ├──────────────► Status History
      │
      ▼
Production
      │
      ├──────────────► Production Stage
      │
      └──────────────► Production History
```

This structure gives administrators visibility into both:

1. The commercial order status.
2. The internal production progress.

---

# Architecture

The backend follows a layered architecture designed to keep responsibilities separated.

```text
┌─────────────────────────────────────┐
│              API Route              │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│             Controller              │
│         HTTP / API Coordination     │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│            Form Request             │
│       Validation & Authorization    │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│               Service               │
│        Business Logic / Workflow    │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│             Repository              │
│          Data Persistence            │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│              Eloquent               │
│               Models                │
└──────────────────┬──────────────────┘
                   │
                   ▼
┌─────────────────────────────────────┐
│              Database               │
└─────────────────────────────────────┘
```

Additional architectural components:

```text
Policy
  └── Authorization

API Resource
  └── Response Transformation

Service
  └── Business Workflows

Repository
  └── Persistence

Form Request
  └── Validation

Sanctum
  └── API Authentication
```

The repository contains a dedicated architectural reference in:

`BACKEND-ARCHITECTURE.md`

Developers should read this document before introducing architectural changes.

---

# Authentication Architecture

The store separates customer and administrator authentication.

## Customer Authentication

```text
Customer
   │
   ▼
Customer Authentication
   │
   ▼
Laravel Sanctum
   │
   ▼
Customer Protected API
```

Customer-protected endpoints use the customer authentication guard.

## Administrator Authentication

```text
Administrator
      │
      ▼
Admin Authentication
      │
      ▼
Laravel Sanctum
      │
      ▼
Admin Protected API
```

Administrator APIs are protected using the dedicated admin authentication guard.

This separation helps prevent customer and administrator responsibilities from being mixed.

---

# API Architecture

The API is organized around three main areas:

```text
/api
│
├── Customer Authentication
│
├── /customer
│     └── Customer Store Operations
│
└── /admin
      └── Store Administration
```

The current route definitions include dedicated areas for customer authentication, administrator authentication, customer operations, and administrator management.

---

# API Modules

| Area           | Main Responsibilities            |
| -------------- | -------------------------------- |
| Authentication | Customer & Admin authentication  |
| Products       | Product catalog                  |
| Categories     | Product categorization           |
| Attributes     | Product attributes and values    |
| Media          | Product image/media management   |
| Customization  | Product customization            |
| Cart           | Shopping cart                    |
| Favorites      | Customer favorites               |
| Addresses      | Customer addresses               |
| Orders         | Customer & Admin orders          |
| Production     | Production stages and history    |
| Payments       | Payment management               |
| Reviews        | Reviews and moderation           |
| Notifications  | Customer/Admin notifications     |
| Customers      | Customer management              |
| Raw Materials  | Store material management        |
| Colors         | Product colors                   |
| Patterns       | Product design patterns          |
| Roles          | Administrator roles              |
| Permissions    | Access permissions               |
| Activity Logs  | Administrative activity tracking |
| Settings       | Store configuration              |
| Exchange Rates | Currency-related data            |

---

# Technology Stack

## Backend

* **Laravel 12**
* **PHP 8.2+**
* **Laravel Sanctum 4.x**
* **Eloquent ORM**
* **RESTful API**

The current `composer.json` declares Laravel 12, PHP `^8.2`, and Laravel Sanctum `^4.3`.

## Development & Quality

The project includes development and quality tooling such as:

* PHPUnit
* ParaTest
* Larastan
* Laravel Pint
* Faker
* Mockery
* Laravel Sail
* Laravel Pail

These dependencies are defined in the repository's Composer configuration.

## Frontend Tooling

The project also contains the tooling required for frontend asset development through:

* Vite
* npm
* Laravel Vite integration

---

# Project Structure

```text
warqah_jitha1/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   │
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   ├── Services/
│   └── ...
│
├── bootstrap/
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
├── routes/
│
├── storage/
├── tests/
│
├── BACKEND-ARCHITECTURE.md
├── app-tree.txt
├── composer.json
├── package.json
├── phpstan.neon
└── phpunit.xml
```

The current repository contains dedicated application, database, routes, tests, architecture documentation, and configuration directories/files.

---

# Requirements

Before installing the project, make sure the development environment includes:

* PHP 8.2 or higher
* Composer
* Node.js
* npm
* PostgreSQL or the configured relational database
* Git

Check your installed versions:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

---

# Installation

## 1. Clone the Repository

```bash
git clone https://github.com/Kurapikao0/warqah_jitha1.git
```

```bash
cd warqah_jitha1
```

---

## 2. Install PHP Dependencies

```bash
composer install
```

---

## 3. Configure Environment

### Linux / macOS

```bash
cp .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

# Database Configuration

Configure the database in `.env`.

Example:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=warqah_jitha
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate
```

For a development database that can safely be recreated:

```bash
php artisan migrate:fresh
```

If seed data is available and intended for the environment:

```bash
php artisan db:seed
```

Or:

```bash
php artisan migrate:fresh --seed
```

> Never run destructive database commands against a production database.

---

# Storage

If product and review media use Laravel's public filesystem:

```bash
php artisan storage:link
```

---

# Frontend Assets

Install Node dependencies:

```bash
npm install
```

Development:

```bash
npm run dev
```

Production build:

```bash
npm run build
```

---

# Run the Store Backend

Start Laravel:

```bash
php artisan serve
```

The API will normally be available at:

```text
http://127.0.0.1:8000
```

The repository also defines a combined development command:

```bash
composer run dev
```

This development workflow starts the Laravel server, queue listener, application logs, and Vite development process concurrently.

---

# Testing

Run the test suite:

```bash
php artisan test
```

Or:

```bash
composer test
```

For parallel testing:

```bash
vendor/bin/paratest
```

---

# Code Quality

## Laravel Pint

Format the project:

```bash
vendor/bin/pint
```

Check formatting:

```bash
vendor/bin/pint --test
```

## Larastan

Run static analysis:

```bash
vendor/bin/phpstan analyse
```

---

# Security

Security is an important part of the store architecture.

The project uses:

* Laravel Sanctum
* Separate authentication guards
* Protected customer APIs
* Protected administrator APIs
* Form Request validation
* Authorization policies
* Authentication throttling
* Password reset workflows
* Verification workflows
* Activity logging

Authentication endpoints currently apply request throttling to reduce repeated abusive requests.

### Never Commit Secrets

Do not commit:

```text
.env
Database passwords
API keys
Access tokens
Private credentials
Production secrets
```

Use `.env.example` to document required environment variables.

---

# Development Principles

## Thin Controllers

Controllers should coordinate HTTP requests rather than contain large business workflows.

```text
Request
   ↓
Controller
   ↓
Service
   ↓
Repository
```

---

## Form Requests

Validation belongs in dedicated Form Request classes.

```text
HTTP Request
     ↓
Form Request
     ↓
Validated Data
```

---

## Services

Services contain application and business workflows.

They are responsible for coordinating operations such as:

* Business rules
* Transactions
* Multiple repositories
* Complex workflows
* Notifications

---

## Repositories

Repositories handle persistence-related operations.

They should not become a replacement for Services.

---

## API Resources

API Resources define the representation returned to the frontend.

This keeps API responses explicit and consistent.

---

## Policies

Policies handle authorization decisions and protect resources from unauthorized access.

---

# Architectural Rule

The most important development rule of this repository is:

> **Extend the existing architecture. Do not unnecessarily replace it.**

Before modifying the backend, developers and AI coding agents should read:

```text
BACKEND-ARCHITECTURE.md
```

The architecture document is the project's reference for backend structure and development rules.

---

# API Development Workflow

When adding a new feature, follow this general workflow:

```text
1. Understand the requirement
          ↓
2. Inspect the existing implementation
          ↓
3. Read the architecture documentation
          ↓
4. Identify the affected Model
          ↓
5. Create/update Form Request
          ↓
6. Implement Service logic
          ↓
7. Implement Repository operations
          ↓
8. Add/update Policy if required
          ↓
9. Add/update API Resource
          ↓
10. Add Controller endpoint
          ↓
11. Register the route
          ↓
12. Add tests
          ↓
13. Run static analysis
          ↓
14. Format the code
          ↓
15. Review the final changes
```

---

# Working With the Frontend

The backend is designed to serve a separate frontend application.

A frontend such as React can consume:

```text
/api/customer/*
/api/admin/*
```

The responsibilities are intentionally separated.

### Frontend

```text
UI
State Management
Forms
User Experience
API Client
```

### Backend

```text
Authentication
Validation
Authorization
Business Logic
Transactions
Persistence
API Responses
```

This separation allows the store's frontend to evolve independently from the Laravel backend.

---

# Current Development Status

**Warqah Wa Jitha is an actively developed e-commerce store.**

The repository currently contains substantial backend infrastructure for:

* Customer authentication
* Administrator authentication
* Product catalog
* Product categories
* Product attributes
* Product media
* Product customization
* Shopping cart
* Favorites
* Addresses
* Orders
* Production stages
* Payments
* Reviews
* Notifications
* Customers
* Raw materials
* Colors
* Design patterns
* Roles
* Permissions
* Activity logs
* System settings
* Exchange rates

The route definitions currently expose dedicated customer and administrator APIs covering these areas.

> The source code, migrations, routes, and tests are the authoritative references for the exact implementation status of each feature.

---

# Roadmap

The following roadmap represents areas that can be continued as the store evolves:

* [ ] Complete remaining backend workflows
* [ ] Expand automated test coverage
* [ ] Complete product media workflows
* [ ] Complete customization workflows
* [ ] Strengthen authorization coverage
* [ ] Expand API documentation
* [ ] Integrate the production frontend
* [ ] Perform end-to-end testing
* [ ] Add continuous integration
* [ ] Add automated code-quality checks
* [ ] Prepare production deployment
* [ ] Improve monitoring and observability
* [ ] Complete production-ready documentation

---

# Documentation

| Document                  | Description                                                   |
| ------------------------- | ------------------------------------------------------------- |
| `README.md`               | Project overview, architecture, installation, and development |
| `BACKEND-ARCHITECTURE.md` | Backend architecture and development rules                    |
| `app-tree.txt`            | Application structure reference                               |
| `composer.json`           | PHP dependencies and scripts                                  |
| `package.json`            | Node/Vite dependencies and scripts                            |

---

# Repository

**GitHub Repository**

https://github.com/Kurapikao0/warqah_jitha1

---

# License

This project is licensed under the **MIT License**.

See the `LICENSE` file for more information.

---

# Project Summary

| Property              | Details                                 |
| --------------------- | --------------------------------------- |
| Project               | ورقة وجذع                               |
| English Name          | Warqah Wa Jitha                         |
| Type                  | Arabic E-Commerce Store                 |
| Target Market         | Yemeni Market                           |
| Backend               | Laravel 12                              |
| Language              | PHP 8.2+                                |
| API                   | RESTful API                             |
| Authentication        | Laravel Sanctum                         |
| Architecture          | Layered Service–Repository Architecture |
| Customer API          | Yes                                     |
| Admin API             | Yes                                     |
| Customization         | Supported                               |
| Orders                | Supported                               |
| Payments              | Supported                               |
| Reviews               | Supported                               |
| Product Media         | Supported                               |
| Production Management | Supported                               |

---

<p align="center">
  <strong>Warqah Wa Jitha</strong>
  <br>
  Arabic E-Commerce Store
  <br><br>
  <em>Built with Laravel 12, RESTful APIs, and a maintainable layered architecture.</em>
</p>
