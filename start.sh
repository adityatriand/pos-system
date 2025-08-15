#!/bin/bash

echo "🚀 Starting POS System..."

# Check if Docker is running
if ! docker info >/dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Build and start containers
echo "📦 Building and starting containers..."
docker-compose up --build -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
until docker exec pos-mysql mysql -u pos_user -ppassword -e "SELECT 1" >/dev/null 2>&1; do
    sleep 2
done

# Run Laravel setup commands
echo "🔧 Setting up Laravel backend..."

# Install composer dependencies
docker exec pos-laravel-app composer install

# Generate application key if not set
docker exec pos-laravel-app php artisan key:generate --force

# Run migrations
echo "📊 Running database migrations..."
docker exec pos-laravel-app php artisan migrate --force

# Seed database (optional)
echo "🌱 Seeding database..."
docker exec pos-laravel-app php artisan db:seed

# Clear caches
docker exec pos-laravel-app php artisan config:clear
docker exec pos-laravel-app php artisan cache:clear

echo ""
echo "✅ POS System is ready!"
echo ""
echo "📱 Frontend: http://localhost:3000"
echo "🔗 Backend API: http://localhost:8080/api"
echo "🗄️  Database: localhost:3306"
echo ""
echo "Database credentials:"
echo "  - Database: pos_system"
echo "  - User: pos_user"
echo "  - Password: password"
echo ""
echo "To stop the system, run: docker-compose down"