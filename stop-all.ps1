Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  MedTech Microservices - Stop All Services" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan

# Stop services in reverse order

Write-Host ""
Write-Host "🛑 Stopping UI Service..." -ForegroundColor Yellow
Push-Location uiservice
docker compose down
Pop-Location

Write-Host ""
Write-Host "🛑 Stopping Product Service..." -ForegroundColor Yellow
Push-Location productservice
docker compose down
Pop-Location

Write-Host ""
Write-Host "🛑 Stopping Order Service..." -ForegroundColor Yellow
Push-Location orderservice
docker compose down
Pop-Location

Write-Host ""
Write-Host "🛑 Stopping User Service..." -ForegroundColor Yellow
Push-Location userservice
docker compose down
Pop-Location

Write-Host ""
Write-Host "🛑 Stopping RabbitMQ..." -ForegroundColor Yellow
Push-Location rabbitmq
docker compose down
Pop-Location

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  ✅ All services stopped successfully!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
