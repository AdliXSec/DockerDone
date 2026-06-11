#!/bin/bash

set -e  # Stop the script if any command fails

echo "============================================"
echo "  MedTech Microservices - Stop All Services"
echo "============================================"

# Stop services in reverse order

echo ""
echo "🛑 Stopping UI Service..."
cd uiservice && docker compose down && cd ..

echo ""
echo "🛑 Stopping Product Service..."
cd productservice && docker compose down && cd ..

echo ""
echo "🛑 Stopping Order Service..."
cd orderservice && docker compose down && cd ..

echo ""
echo "🛑 Stopping User Service..."
cd userservice && docker compose down && cd ..

echo ""
echo "🛑 Stopping RabbitMQ..."
cd rabbitmq && docker compose down && cd ..

echo ""
echo "============================================"
echo "  ✅ All services stopped successfully!"
echo "============================================"
