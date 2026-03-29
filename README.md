# Book Library API

A RESTful API built with **Laravel 11** and **PHP 8.3** for managing a book library. Track your entire book collection with full CRUD support, search, filtering, and interactive Swagger UI documentation.

---

## Features

- **Full CRUD** — GET / POST / PATCH / DELETE for books
- **Search & Filter** — query by title, author, publisher, or genre
- **Swagger UI** — interactive API documentation at `/api/documentation`
- **PHPUnit Tests** — comprehensive feature test coverage
- **Docker** — one-command setup with `docker-compose`
- **Fixtures & Migrations** — seeded with real-world book data

---

## Book Model

| Field          | Type             | Description                      |
|----------------|------------------|----------------------------------|
| `title`        | string (max 255) | Book title                       |
| `publisher`    | string (max 255) | Publisher name                   |
| `author`       | string (max 255) | Author full name                 |
| `genre`        | string (max 100) | Genre / category                 |
| `published_at` | date (`Y-m-d`)   | Original publication date        |
| `word_count`   | integer (≥ 1)    | Total number of words            |
| `price`        | decimal (USD)    | Retail price in US Dollars       |

---

## API Endpoints

| Method   | Endpoint              | Description                          |
|----------|-----------------------|--------------------------------------|
| `GET`    | `/api/books`          | List all books (search / filter)     |
| `POST`   | `/api/books`          | Add a new book                       |
| `GET`    | `/api/books/{id}`     | Get a single book by ID              |
| `PATCH`  | `/api/books/{id}`     | Partially update a book              |
| `DELETE` | `/api/books/{id}`     | Remove a book                        |

### Query Parameters for `GET /api/books`

| Parameter | Type   | Description                                                   |
|-----------|--------|---------------------------------------------------------------|
| `search`  | string | Case-insensitive search across title, author, publisher, genre |
| `genre`   | string | Exact genre filter (e.g. `Fantasy`, `Science Fiction`)        |

**Examples:**
```
GET /api/books?search=tolkien
GET /api/books?genre=Fiction
GET /api/books?search=orwell&genre=Science+Fiction
```

---

## Quick Start (Docker — Recommended)

### Prerequisites
- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/) v2

### 1. Clone the repository

```bash
git clone https://github.com/antonryazanov/book-library-api.git
cd book-library-api
```

### 2. Run the automated setup

```bash
make setup
```

This single command:
1. Builds the PHP 8.3 + Nginx Docker images
2. Starts all containers (app, nginx, MySQL)
3. Installs Composer dependencies
4. Generates the Laravel application key
5. Runs database migrations
6. Seeds the database with 25 books (10 real-world fixtures + 15 random)
7. Generates the Swagger OpenAPI specification

### 3. Access the application

| URL                                        | Description         |
|--------------------------------------------|---------------------|
| http://localhost:8080/api/books            | REST API            |
| http://localhost:8080/api/documentation    | Swagger UI          |

---

## Manual Setup (without Docker)

### Prerequisites
- PHP ≥ 8.3 with extensions: `pdo_mysql`, `mbstring`, `bcmath`
- [Composer](https://getcomposer.org/) ≥ 2
- MySQL ≥ 8.0 **or** SQLite (for local testing)

### Steps

```bash
# 1. Clone
git clone https://github.com/antonryazanov/book-library-api.git
cd book-library-api

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env with your database credentials
#    DB_HOST=127.0.0.1
#    DB_DATABASE=book_library
#    DB_USERNAME=your_user
#    DB_PASSWORD=your_password

# 5. Run migrations
php artisan migrate

# 6. Seed the database (optional but recommended)
php artisan db:seed

# 7. Generate Swagger docs
php artisan l5-swagger:generate

# 8. Start the development server
php artisan serve
```

The API will be available at `http://localhost:8000/api/books`.

---

## Running Tests

Tests use an **in-memory SQLite** database — no MySQL setup required.

```bash
# With Docker
make test

# Without Docker (host PHP)
php artisan test

# With verbose output
php artisan test --verbose
```

The test suite covers:
- Listing books (empty list, full list, sorted results)
- Search and genre filtering
- Creating books (success + all validation rules)
- Retrieving a single book + 404 handling
- Partial updates (PATCH semantics) + validation + 404
- Deleting books + 404 handling

---

## Docker Commands Reference

| Command         | Description                                              |
|-----------------|----------------------------------------------------------|
| `make setup`    | Full first-time setup (build + migrate + seed + swagger) |
| `make up`       | Start all containers                                     |
| `make down`     | Stop all containers                                      |
| `make shell`    | Open a shell inside the PHP container                    |
| `make logs`     | Tail container logs                                      |
| `make test`     | Run PHPUnit tests inside Docker                          |
| `make migrate`  | Run pending database migrations                          |
| `make seed`     | Seed the database                                        |
| `make fresh`    | Drop all tables, re-migrate, and re-seed                 |
| `make swagger`  | Regenerate the Swagger spec                              |
| `make clean`    | Remove all containers and the DB volume (full reset)     |

---

## Project Structure

```
book-library-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/BookController.php   # CRUD actions + Swagger annotations
│   │   ├── Requests/
│   │   │   ├── StoreBookRequest.php             # Validation for POST
│   │   │   └── UpdateBookRequest.php            # Validation for PATCH
│   │   └── Resources/BookResource.php           # JSON response transformer
│   └── Models/Book.php                          # Eloquent model
├── database/
│   ├── factories/BookFactory.php                # Fake data generator
│   ├── migrations/                              # Schema migrations
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── BookSeeder.php                       # Real-world book fixtures
├── routes/api.php                               # API route registration
├── tests/Feature/BookApiTest.php                # PHPUnit feature tests
├── docker/
│   ├── nginx/default.conf                       # Nginx config
│   └── php/php.ini                              # PHP runtime config
├── docker-compose.yml
├── Dockerfile
└── Makefile                                     # Developer convenience commands
```

---

## Example Requests

**Create a book:**
```bash
curl -X POST http://localhost:8080/api/books \
  -H "Content-Type: application/json" \
  -d '{
    "title": "The Great Gatsby",
    "publisher": "Scribner",
    "author": "F. Scott Fitzgerald",
    "genre": "Fiction",
    "published_at": "1925-04-10",
    "word_count": 47094,
    "price": 12.99
  }'
```

**Update only the price:**
```bash
curl -X PATCH http://localhost:8080/api/books/1 \
  -H "Content-Type: application/json" \
  -d '{"price": 9.99}'
```

**Search for books:**
```bash
curl "http://localhost:8080/api/books?search=tolkien"
```

---

## License

MIT
