# PowerShell script to refresh the Laravel application: regenerate Wayfinder types, reset permissions cache,
# clear optimizations, and perform a fresh migration with seeding.

# Ensure we're in the project root directory
$ScriptDir = Split-Path $MyInvocation.MyCommand.Path -Parent
Set-Location "$ScriptDir/.."

Write-Host "Starting application refresh..."

# Perform fresh migration with seeding (destructive operation) - run first
Write-Host "Performing fresh migration with seeding..."
php artisan migrate:fresh --seed

# Regenerate Wayfinder types for frontend synchronization
Write-Host "Generating Wayfinder types..."
php artisan wayfinder:generate

# Reset permission cache
Write-Host "Resetting permission cache..."
php artisan permission:cache-reset

# Clear all optimizations
Write-Host "Clearing optimizations..."
php artisan optimize:clear

# Build frontend assets
Write-Host "Building frontend assets..."
npm run build

Write-Host "Application refresh completed successfully."