Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  MedTech Microservices - Start All Services" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan

# Create Docker network if it doesn't exist
Write-Host ""
Write-Host "🔧 Creating Docker network 'medtech-network' if not exists..." -ForegroundColor Yellow
$networkExists = docker network ls --filter name=medtech-network --format "{{.Name}}" 2>$null
if (-not $networkExists) {
    docker network create medtech-network
    Write-Host "   Network created." -ForegroundColor Green
} else {
    Write-Host "   Network already exists." -ForegroundColor Gray
}

# 1. Start RabbitMQ
Write-Host ""
Write-Host "🚀 Starting RabbitMQ..." -ForegroundColor Yellow
Push-Location rabbitmq
docker compose up -d
Pop-Location

# Wait for RabbitMQ
Write-Host "⏳ Waiting for RabbitMQ to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# 2. Start User Service
Write-Host ""
Write-Host "🚀 Starting User Service..." -ForegroundColor Yellow
Push-Location userservice
docker compose up -d --build
Pop-Location

# 3. Start Order Service
Write-Host ""
Write-Host "🚀 Starting Order Service..." -ForegroundColor Yellow
Push-Location orderservice
docker compose up -d --build
Pop-Location

# 4. Start Product Service
Write-Host ""
Write-Host "🚀 Starting Product Service..." -ForegroundColor Yellow
Push-Location productservice
docker compose up -d --build
Pop-Location

# 5. Start UI Service
Write-Host ""
Write-Host "🚀 Starting UI Service..." -ForegroundColor Yellow
Push-Location uiservice
docker compose up -d --build
Pop-Location

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  ✅ All containers started! Running Setup..." -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green

# Wait for containers to be fully ready before exec
Write-Host "⏳ Waiting 15 seconds for containers to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

Write-Host ""
Write-Host "🔧 Setting up User Service (Python)..." -ForegroundColor Yellow
docker exec -it medtech-userservice python init_db.py
docker exec -it medtech-userservice python seed.py

Write-Host ""
Write-Host "🔧 Setting up UI Service (Laravel)..." -ForegroundColor Yellow
docker exec -it medtech-uiservice php artisan key:generate --force
docker exec -it medtech-uiservice php artisan optimize:clear
docker exec -it medtech-uiservice php artisan migrate --force

Write-Host ""
Write-Host "🔧 Setting up Order Service (Laravel)..." -ForegroundColor Yellow
docker exec -it medtech-orderservice php artisan key:generate --force
docker exec -it medtech-orderservice php artisan optimize:clear
docker exec -it medtech-orderservice php artisan migrate --force

Write-Host ""
Write-Host "🔧 Setting up Product Service (Laravel)..." -ForegroundColor Yellow
docker exec -it medtech-productservice php artisan key:generate --force
docker exec -it medtech-productservice php artisan optimize:clear
docker exec -it medtech-productservice php artisan migrate --force
docker exec -it medtech-productservice php artisan db:seed --class=ObatSeeder --force

Write-Host ""
Write-Host "🚀 Starting Queue Workers in background..." -ForegroundColor Yellow
docker exec -d medtech-orderservice php artisan queue:work rabbitmq_users
docker exec -d medtech-productservice php artisan queue:work rabbitmq --queue=product_stock_queue

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  🎉 All setup completed successfully!" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Access the services at:"
Write-Host "  - UI Service:          http://localhost:8000"
Write-Host "  - Order Service API:   http://localhost:8001"
Write-Host "  - Product Service API: http://localhost:8002"
Write-Host "  - User Service API:    http://localhost:5001"
Write-Host "  - RabbitMQ Management: http://localhost:15672"
Write-Host ""
