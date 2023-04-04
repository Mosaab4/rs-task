set -e

export APP_SERVICE=${APP_SERVICE:-"laravel.test"}
export DB_PORT=${DB_PORT:-3306}
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

echo "=== Copy .env  ==="
cp .env.example .env
echo "=== copy is done!  ==="

echo "=== Execute docker-compose build... ==="
docker-compose up -d
echo "=== docker-compose build is done!! ==="

echo "=== Installing composer... ==="
docker-compose run laravel.test composer update
echo "=== Composer build is done!! ==="

echo "==== Generating the app key ==="
./vendor/bin/sail php artisan key:generate
echo "==== App key generating is done!! ==="

echo "==== Migrating and seed database ==="
./vendor/bin/sail php artisan migrate:fresh --seed
echo "==== Migrating and seed database is done!! ==="
