#!/bin/bash

set -e

echo "🔨 Building Laravel application..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if we're in the correct directory
if [ ! -f "src/artisan" ]; then
    print_error "artisan file not found. Are you in the project root?"
    exit 1
fi

# Build steps
print_status "Installing Composer dependencies..."
cd src
composer install --optimize-autoloader --no-dev

print_status "Installing NPM dependencies..."
npm ci

print_status "Building frontend assets..."
npm run build

print_status "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

print_status "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Running tests..."
php artisan test

print_status "Build completed successfully! ✅"