# Loan Lifecycle - Installment Loan Management System

A Lumen-based REST API for managing installment loans with complete lifecycle support: creation, approval, and repayment tracking.

## 🎯 Features

- **Loan Creation**: Initialize loans with amount and term
- **Loan Approval**: Generate installment schedules with interest calculation
- **Payment Processing**: Track payments and automatically update loan status
- **Complete Test Coverage**: 18 unit and integration tests
- **Docker Support**: Easy setup with Docker Compose

## 🏗️ Architecture

### Clean Domain Design
- **Domain Exceptions**: Named constructors for business rule violations
- **Service Layer**: `LoanService` and `InstallmentScheduleService` (SRP principle)
- **REST API**: Clean HTTP endpoints with validation

### Loan Lifecycle States
1. **CREATED** - Initial state, no installments
2. **OPEN** - Approved, installments scheduled, accepting payments
3. **REPAID** - Fully paid, no remaining debt

## 📊 Installment Calculation

Uses standard amortization formula for equal monthly payments:

```
Monthly Payment = [P × (r × (1 + r)^n)] / [(1 + r)^n - 1]
```

Where:
- **P** = Principal amount
- **r** = Monthly interest rate (Annual Rate / 12)
- **n** = Number of payments

**Interest Rate**: 10% APR (0.833% monthly)

### Example Schedule
For a loan of **1000** over **6 months**:

| # | Principal | Interest | Total  |
|---|-----------|----------|--------|
| 1 | 163.23    | 8.33     | 171.56 |
| 2 | 164.59    | 6.97     | 171.56 |
| 3 | 165.96    | 5.60     | 171.56 |
| 4 | 167.34    | 4.22     | 171.56 |
| 5 | 168.74    | 2.82     | 171.56 |
| 6 | 170.14    | 1.42     | 171.56 |

## 🚀 Quick Start

### Docker (Recommended)

```bash
# Start all services
make setup

# Application available at http://localhost:8080
```

That's it! The setup command will:
- Build Docker images
- Start containers (app, webserver, db)
- Install Composer dependencies
- Run migrations and seeders

### Manual Setup

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env

# Run migrations
php artisan migrate --seed

# Run tests
./vendor/bin/phpunit
```

## 📝 API Endpoints

### Create Loan
```http
POST /loans
Content-Type: application/json

{
  "amount": 1000,
  "term": 6
}
```

### Approve Loan
```http
POST /loans/{id}/approve
```

### Add Payment
```http
POST /loans/{id}/payments
Content-Type: application/json

{
  "amount": 171.56
}
```

### Get Loan Details
```http
GET /loans/{id}
```

## 🧪 Testing

All 18 tests passing ✅

```bash
# Run all tests
make test

# Or with PHPUnit directly
./vendor/bin/phpunit
```

### Test Coverage
- ✅ Loan creation and validation
- ✅ Loan approval and state transitions
- ✅ Installment schedule generation
- ✅ Payment processing and debt calculation
- ✅ Business rule violations (domain exceptions)
- ✅ HTTP endpoint integration

## 🛠️ Tech Stack

- **Framework**: Lumen 8.x (Laravel micro-framework)
- **PHP**: 7.4
- **Database**: MySQL 5.7 / SQLite (tests)
- **Testing**: PHPUnit
- **Docker**: PHP-FPM + Nginx + MySQL

## 📦 Project Structure

```
├── app/
│   ├── Exceptions/Domain/      # Business rule exceptions
│   ├── Http/Controllers/       # HTTP layer
│   ├── Models/                 # Eloquent models
│   └── Services/               # Business logic
├── database/
│   ├── migrations/             # Database schema
│   └── factories/              # Test data factories
├── tests/                      # PHPUnit tests
├── docs/                       # Documentation
└── docker/                     # Docker configuration
```

## 🎓 Key Implementation Details

### Domain Exceptions
Custom exceptions with named constructors for clear business rules:
```php
throw LoanCanNotBeApprovedException::becauseStatusIsNot($loan, Loan::STATUS_CREATED);
throw PaymentCanNotBeAddedException::becauseAmountExceedsDebt($amount, $loan->remaining_amount);
```

### Installment Schedule Service
Separated concern (SRP) for calculating payment schedules:
- PMT formula implementation
- Interest calculation per installment
- Clean, testable mathematics

### Clean Controller Layer
Thin controllers with validation, delegating to services:
```php
public function approve(int $id, LoanService $loanService)
{
    return $loanService->approveLoan($id);
}
```

## 🐳 Docker Commands

```bash
make setup          # Complete setup
make test           # Run tests
make shell          # Access PHP container
make logs           # View logs
make migrate        # Run migrations
make migrate-fresh  # Fresh migrations + seeds
```

## 📚 Documentation

- [Task Instructions](docs/Instructions.md) - Original challenge requirements
- [Postman Collection](docs/Lumen_Loan_API.postman_collection.json) - API endpoints

## 🤝 Contributing

This is a recruitment task showcase. Feel free to use it as reference for:
- Clean architecture in Lumen/Laravel
- Domain-driven design principles
- Financial calculations (PMT formula)
- Test-driven development

## 📄 License

MIT License - free to use for educational purposes.

---

**Built with ❤️ using Lumen Framework**
