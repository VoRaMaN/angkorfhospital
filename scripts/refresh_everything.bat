@echo off
REM Batch script to refresh the Laravel application: regenerate Wayfinder types, reset permissions cache,
REM clear optimizations, and perform a fresh migration with seeding.

REM Ensure we're in the project root directory
cd /d "%~dp0\.."

echo Starting application refresh...

REM Perform fresh migration with seeding (destructive operation) - run first
echo Performing fresh migration with seeding...
php artisan migrate:fresh --seed

REM Regenerate Wayfinder types for frontend synchronization
echo Generating Wayfinder types...
php artisan wayfinder:generate

REM Reset permission cache
echo Resetting permission cache...
php artisan permission:cache-reset

REM Clear all optimizations
echo Clearing optimizations...
php artisan optimize:clear

REM Build frontend assets
echo Building frontend assets...
npm run build

echo Application refresh completed successfully.
pause