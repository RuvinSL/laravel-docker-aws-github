# Build and run locally with Docker
docker-compose up -d

# Let’s say you updated your PHP code or changed your Dockerfile. You’d run:
docker-compose up -d --build

# Want to see logs after that? Just run:
docker-compose logs -f

In your docker-compose.yml check localhost port eg: 8080
http://localhost:8000

 # Rebuild and Restart
docker-compose down -v  # Optional: reset everything 🧠 When to use -v --> Use it when you want a full reset, including: Database data, Uploaded files, Cached files
docker-compose up -d --build

# Restart without rebuild
docker-compose down -v   # Optional: reset everything
docker-compose up -d     # Start containers
docker-compose exec app php artisan migrate

# login to Mysql inside docker
docker-compose exec db mysql -u root -p

# when you change laravel config settings clear cache before run other command
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear


docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed


----------------------- REDIS-------------------------
# install dependency 
docker-compose exec app composer require predis/predis

# Update .env
REDIS_CLIENT=predis
REDIS_HOST=redis # service name of your Redis container in docker-compose.yml
REDIS_PORT=6379
REDIS_PASSWORD=null

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# check Redis running
 docker-compose exec redis redis-cli ping

# also use the below command to list pods and check service
docker-compose ps

# test redia in laravel
docker-compose exec app php artisan tinker
cache()->put('hello', 'redis', 10);
cache()->get('hello'); // should return "redis"


