# Translation Management Service (Laravel API)

## Overview

This project is an **API-driven Translation Management Service** built with Laravel. It is designed to demonstrate clean architecture, scalability, security, and performance when working with large datasets (100k+ records).

The service allows managing translations across multiple locales, tagging translations for contextual usage, searching efficiently, and exporting translations in JSON format for frontend applications (e.g. Vue.js, React).

Performance, maintainability, and clear design decisions were prioritized throughout the implementation.

---

## Features

* Store translations for multiple locales (e.g. `en`, `en_US`, `fr`)
* Tag translations for contextual usage (`web`, `mobile`, `auth`, etc.)
* Secure API using token-based authentication
* Create, update, view, and search translations
* Optimized search by locale, key, tag, and content
* JSON export endpoint for frontend consumption
* Handles 100k+ translation records efficiently
* Fully API-based (no UI)

---

## Tech Stack

* **Laravel** (API-first)
* **PHP 8.2**
* **MySQL / PostgreSQL**
* **Laravel Sanctum** (token authentication)
* **Redis** (optional, for caching)
* **Docker** (optional setup)

---

## Architecture & Design Choices

### Layered Architecture

The application follows a clear separation of concerns:

* **Controllers** – Handle HTTP requests and responses only
* **Request Classes** – Validation and input sanitization
* **Services** – Business logic and orchestration
* **Repositories** – Optimized database queries
* **Resources** – Consistent API response formatting

This structure follows **SOLID principles** and keeps the codebase scalable and testable.

---

### Database Design

Core tables:

* `locales` – Stores available languages
* `translations` – Stores translation keys and values
* `tags` – Contextual labels
* `tag_translation` – Pivot table (many-to-many)

Indexes are applied on:

* `translations(locale_id, key)`
* `translations(key)`
* `tags(name)`
* `tag_translation(tag_id, translation_id)`

These indexes ensure fast query execution and predictable performance at scale.

---

### Search Strategy (Performance-Safe)

Search operations are designed to avoid full table scans:

* At least one indexed filter is required (locale, key, or tag)
* Prefix matching is used for translation keys
* Content search (`value`) is restricted with a minimum length
* Pagination is enforced on all search endpoints

Tag-based searches use explicit SQL joins instead of subqueries to improve performance with large datasets.

---

### JSON Export Strategy

The export endpoint is optimized for frontend usage:

* Returns flat key-value JSON (`{ key: value }`)
* No relationships are loaded
* Uses `pluck()` for memory efficiency
* Cached per locale
* Cache is invalidated on create/update/delete

This ensures export responses remain under **500ms**, even with large datasets.

---

## API Endpoints

### Authentication

```
POST /api/auth/token
```

Returns a bearer token for authenticated requests.

---

### Translations

```
POST   /api/translations
PUT    /api/translations/{id}
GET    /api/translations/{id}
```

---

### Search

```
GET /api/translations/search
```

Query parameters:

* `locale`
* `key`
* `tag`
* `content` (minimum 3 characters)

---

### Export

```
GET /api/translations/export?locale=en
```

Returns:

```json
{
  "auth.login.title": "Login",
  "auth.logout": "Logout"
}
```

---

## Performance Considerations

* Indexed queries only
* Controlled usage of `LIKE` statements
* Explicit joins for tag searches
* Pagination enforced
* Caching applied to export endpoint

Target response times:

* Standard endpoints: **< 200ms**
* Export endpoint: **< 500ms**

---

## Database Seeding & Scalability Testing

A custom seeder and/or artisan command is included to generate **100,000+ translations** for scalability testing.

```
php artisan db:seed --class=LargeTranslationSeeder
```

This allows realistic performance validation under heavy data load.

---

## Testing

Testing is implemented using Laravel’s built-in PHPUnit support.

### Test Coverage

* Feature tests for all API endpoints
* Unit tests for service logic
* Performance-oriented assertions for critical endpoints

Example performance validation:

* Export endpoint response time assertion
* Search endpoint response time assertion

Overall test coverage target: **95%+**

---

## API Documentation

The API is documented using **OpenAPI / Swagger** (if enabled), making it easy to explore and test endpoints.

---

## Setup Instructions

### Without Docker

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

---

### With Docker (Optional)

```

docker-compose up -d --build
docker-compose exec app_laravel_api_test php artisan migrate:fresh --seed

```

---

## Security

* Token-based authentication using Laravel Sanctum
* All endpoints protected except token issuance
* Input validation on all requests

---

## View Apis Manualy

check this postman link: www.postman.com/thfg88/workspace/share-api-test

## Final Notes

This project focuses on:

* Clean, readable, and maintainable code
* Real-world performance considerations
* Scalable data handling
* Clear separation of concerns

It is intentionally designed as an **API-only service** to reflect modern backend systems used by frontend frameworks and mobile applications.

---

## Author

**Laravel Senior Developer Code Test Submission**
