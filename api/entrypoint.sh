#!/bin/sh

# Start by running composer install
composer install

php artisan key:generate

php artisan jwt:secret

php artisan storage:link

# Start the PHP development server
php artisan serve --host 0.0.0.0 --port 8000
