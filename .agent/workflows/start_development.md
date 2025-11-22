---
description: Start all Dooeats development servers (Customer, Admin, Restaurant) in new Terminal windows
---
// turbo-all

1. Start the Customer App (Port 8000)
   ```bash
   osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps\" && php artisan serve --host=127.0.0.1 --port=8000"'
   ```

2. Start the Admin App (Port 8001)
   ```bash
   osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps/admin\" && php artisan serve --host=127.0.0.1 --port=8001"'
   ```

3. Start the Restaurant App (Port 8002)
   ```bash
   osascript -e 'tell application "Terminal" to do script "cd \"/Users/enohmichael/Dooeats version 2.0/Web Apps/restaurant\" && php artisan serve --host=127.0.0.1 --port=8002"'
   ```
