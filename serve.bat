@echo off
REM Laravel Serve Script
REM Usage: serve.bat [host] [port]
REM Example: serve.bat 127.0.0.1 8000

set "PATH=%PATH%;C:\xampp\php"

if "%1"=="" (
    set HOST=127.0.0.1
) else (
    set HOST=%1
)

if "%2"=="" (
    set PORT=8000
) else (
    set PORT=%2
)

echo Starting Laravel server on http://%HOST%:%PORT%
php artisan serve --host=%HOST% --port=%PORT%