set -e

echo "=== Copy .env  ==="
cp .env.example .env
echo "=== copy is done!  ==="

echo "=== Execute docker-compose build... ==="
docker-compose up -d --build
echo "=== docker-compose build is done!! ==="

echo "=== Installing composer... ==="
docker-compose run app composer install
echo "=== Composer build is done!! ==="

echo "==== Generating the app key ==="
docker-compose run app php artisan key:generate
docker-compose run app php artisan config:clear
echo "==== App key generating is done!! ==="

echo "==== Migrating and seed database ==="
docker-compose run app php artisan migrate:fresh --seed
echo "==== Migrating and seed database is done!! ==="
