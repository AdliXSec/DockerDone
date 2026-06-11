#!/bin/bash

set -e  # Stop the script if any command fails

echo "============================================"
echo "  MedTech Microservices - Start All Services"
echo "============================================"

# Create Docker network if it doesn't exist
echo ""
echo "🔧 Creating Docker network 'medtech-network' if not exists..."
docker network inspect medtech-network >/dev/null 2>&1 || \
docker network create medtech-network

# 1. Start RabbitMQ
echo ""
echo "🚀 Starting RabbitMQ..."
cd rabbitmq || { echo "Directory rabbitmq not found."; exit 1; }
docker compose up -d
cd ..

# 2. Start User Service (Python/Flask)
echo ""
echo "🚀 Starting User Service..."
cd userservice || { echo "Directory userservice not found."; exit 1; }
docker compose up -d --build
cd ..

# 3. Start Order Service (Laravel)
echo ""
echo "🚀 Starting Order Service..."
cd orderservice || { echo "Directory orderservice not found."; exit 1; }
docker compose up -d --build
cd ..

# 4. Start Product Service (Laravel)
echo ""
echo "🚀 Starting Product Service..."
cd productservice || { echo "Directory productservice not found."; exit 1; }
docker compose up -d --build
cd ..

# 5. Start UI Service (Laravel)
echo ""
echo "🚀 Starting UI Service..."
cd uiservice || { echo "Directory uiservice not found."; exit 1; }
docker compose up -d --build
cd ..

echo ""
echo "============================================"
echo "  ✅ All containers started! Running Setup..."
echo "============================================"

# Wait for containers to be fully ready before exec
echo "⏳ Waiting 15 seconds for containers to initialize..."
sleep 15

echo ""
echo "🔧 Setting up User Service (Python)..."
docker exec -it medtech-userservice python init_db.py || true
docker exec -it medtech-userservice python seed.py || true

echo ""
echo "🔧 Setting up UI Service (Laravel)..."
docker exec -it medtech-uiservice sh -c "php artisan key:generate --force && php artisan optimize:clear && php artisan migrate --force"

echo ""
echo "🔧 Setting up Order Service (Laravel)..."
docker exec -it medtech-orderservice sh -c "php artisan key:generate --force && php artisan optimize:clear && php artisan migrate --force"

echo ""
echo "🔧 Setting up Product Service (Laravel)..."
docker exec -it medtech-productservice sh -c "php artisan key:generate --force && php artisan optimize:clear && php artisan migrate --force && php artisan db:seed --class=ObatSeeder --force"

echo ""
echo "🚀 Starting Queue Workers in background..."
docker exec -d medtech-orderservice php artisan queue:work rabbitmq_users
docker exec -d medtech-productservice php artisan queue:work rabbitmq --queue=product_stock_queue

echo ""
echo "============================================"
echo "  🎉 All setup completed successfully!"
echo "============================================"
echo ""
echo "  Access the services at:"
echo "  - UI Service:          http://localhost:8000"
echo "  - Order Service API:   http://localhost:8001"
echo "  - Product Service API: http://localhost:8002"
echo "  - User Service API:    http://localhost:5001"
echo "  - RabbitMQ Management: http://localhost:15672"
echo ""
