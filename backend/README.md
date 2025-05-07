## IN PRODUCTION CONFIGURATION

git clone https://github.com/anithapadma/rental-system.git

cd rental-system/backend

cp .env.example .env

** change the .env file variables

APP_URL=https://profile.selfmade.one
DB_CONNECTION=sqlite
DB_HOST=mysql.selfmade.ninja
DB_PORT=3306
DB_DATABASE=anitha_rental_system
DB_USERNAME=anitha
DB_PASSWORD=anitha@123

composer install

php artisan key:generate

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

php artisan migrate

php artisan db:seed