.PHONY: setup up down restart logs shell test migrate seed swagger clean

# ─── Full automated setup (recommended first-time command) ───────────────────
# Builds images, starts containers, installs deps, runs migrations + seed, generates Swagger docs
setup: up
	@echo "⏳ Waiting for containers to be ready..."
	@sleep 5
	docker exec book-library-app sh -c "\
		composer install && \
		php artisan key:generate && \
		php artisan migrate --force && \
		php artisan db:seed --force && \
		php artisan l5-swagger:generate \
	"
	@echo ""
	@echo "✅ Setup complete!"
	@echo "   API:        http://localhost:8080/api/books"
	@echo "   Swagger UI: http://localhost:8080/api/documentation"

# ─── Container lifecycle ─────────────────────────────────────────────────────

# Build images and start all containers in detached mode
up:
	docker-compose up -d --build

# Stop and remove containers (data volume is preserved)
down:
	docker-compose down

# Restart all containers
restart:
	docker-compose restart

# ─── Development helpers ─────────────────────────────────────────────────────

# Tail logs from all containers
logs:
	docker-compose logs -f

# Open an interactive shell inside the PHP-FPM container
shell:
	docker exec -it book-library-app sh

# ─── Database ────────────────────────────────────────────────────────────────

# Run pending database migrations
migrate:
	docker exec book-library-app php artisan migrate --force

# Seed the database with book fixtures
seed:
	docker exec book-library-app php artisan db:seed --force

# Rollback all migrations and re-run them fresh (⚠️ destroys all data)
fresh:
	docker exec book-library-app php artisan migrate:fresh --seed

# ─── Tests ───────────────────────────────────────────────────────────────────

# Run the full PHPUnit test suite inside Docker
test:
	docker exec book-library-app php artisan test

# Run tests locally (requires PHP + SQLite installed on host)
test-local:
	php artisan test

# ─── Swagger ─────────────────────────────────────────────────────────────────

# Re-scan annotations and regenerate the OpenAPI spec file
swagger:
	docker exec book-library-app php artisan l5-swagger:generate

# ─── Cleanup ─────────────────────────────────────────────────────────────────

# Remove all containers, networks, AND the database volume (full reset)
clean:
	docker-compose down -v --remove-orphans
