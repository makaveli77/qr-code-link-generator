#!/bin/bash

set -euo pipefail

echo "🚀 Starting QR Code Link Generator Setup..."

echo "📦 Building and starting Docker containers..."
docker compose up -d --build

echo "🔑 Fixing vendor permissions for appuser..."
docker compose exec app chown -R appuser:appuser /var/www/html/vendor || true

echo "📥 Installing PHP dependencies..."
docker compose exec app composer install

echo "📥 Installing Node dependencies..."
docker compose exec app npm install

echo "🗄️ Running database migrations and seeders..."
docker compose exec app php artisan migrate --seed

echo "🧹 Clearing application cache..."
docker compose exec app php artisan cache:clear

echo "🧪 Running the test suite..."
docker compose exec app php artisan test

echo "⚙️ Starting the queue worker (background)..."
docker compose exec -d queue php artisan queue:work

echo ""
echo "✅ Setup complete!"
echo "🌐 App URL: http://localhost:8000"
echo "------------------------------------------------------------------"
