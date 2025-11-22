#!/bin/bash

# Script to refresh the Laravel application: regenerate Wayfinder types, reset permissions cache,
# clear optimizations, and perform a fresh migration with seeding.

set -e  # Exit on any error

# Ensure we're in the project root directory
cd "$(dirname "$0")/.." || exit 1

echo "Starting application refresh..."

# Perform fresh migration with seeding (destructive operation) - run first
echo "Performing fresh migration with seeding..."
php artisan migrate:fresh --seed

# Regenerate Wayfinder types for frontend synchronization
echo "Generating Wayfinder types..."
php artisan wayfinder:generate

# Reset permission cache
echo "Resetting permission cache..."
php artisan permission:cache-reset

# Clear all optimizations
echo "Clearing optimizations..."
php artisan optimize:clear

# Build frontend assets
echo "Building frontend assets..."
npm run build

echo "Application refresh completed successfully."