#!/bin/bash

# Ensure the link exists every time the server boots
php artisan storage:link

# Clear caches just to be safe
php artisan optimize
php artisan config:cache
php artisan route:cache

# Start the actual web server (Apache or PHP-FPM depending on your setup)
apache2-foreground
