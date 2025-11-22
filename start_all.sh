#!/bin/bash

echo "Starting Dooeats Development Servers..."

# Start Customer App
echo "Starting Customer App on port 8000..."
osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps\" && php artisan serve --host=127.0.0.1 --port=8000"'

# Start Admin App
echo "Starting Admin App on port 8001..."
osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps/admin\" && php artisan serve --host=127.0.0.1 --port=8001"'

# Start Restaurant App
echo "Starting Restaurant App on port 8002..."
osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps/restaurant\" && php artisan serve --host=127.0.0.1 --port=8002"'

echo "All servers started in separate Terminal windows."
