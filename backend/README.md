## IN PRODUCTION CONFIGURATION

git clone https://github.com/anithapadma/rental-system.git

cd rental-system/backend

cp .env.example .env

** change the .env file variables

APP_URL=https://profile.selfmade.one
DB_CONNECTION=sqlite
DB_HOST=mysql.selfmade.ninja
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

composer install

php artisan key:generate
composer require tymon/jwt-auth
php artisan jwt:secret
composer require laravel/sanctum

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

php artisan migrate

php artisan db:seed