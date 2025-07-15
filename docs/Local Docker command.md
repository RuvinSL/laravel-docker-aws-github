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

# Access container
docker-compose exec app bash       





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



-----------------------------DOCKER------------------------------------
------------------- ID ONE CONTAINER NOT STARTED ----------------------------
docker ps -a # to list all containers

docker start <containeri_d>



---------------------- GIT------------------------------------------
Safer Approach (fetch + rebase) for developers : 

git fetch origin
git rebase origin/dev  # Resolve any conflicts as they appear

git fetch (Recommended Approach)
git fetch origin
git merge origin/dev     # or rebase
# or
git rebase origin/dev

# Dangerous Approach (plain pull):
git pull origin dev  # May create merge commits automatically # Might trigger merge conflicts unexpectedly

When to Use git stash:
    When you have uncommitted changes but need to pull updates from the remote
    When you need to quickly switch branches without committing half-done work
    When you want to test something without your current changes interfering

# You're working on a feature but need to update
$ git stash push -m "user auth progress"

# Get latest changes
$ git pull --rebase origin main

# Bring back your work
$ git stash pop

# Resolve any conflicts if they occur
# Continue working...

Advanced Stash Tips:
List all stashes:
git stash list

Apply a specific stash without removing it:
git stash apply stash@{n}  # where n is the stash number

Create a branch from a stash:
git stash branch new-branch-name stash@{n}

Clear all stashes: (precaution)
git stash clear

# Here's how to completely reset your local main branch to match the remote repository (as if you just cloned it fresh):
git fetch origin
git checkout main
git reset --hard origin/main
git clean -n
git clean -fd


---------------------- LARAVEL COMMAND----------------------

# in case it laravel Test fails use the below command to generate .env api testing key
php artisan key:generate --env=testing

-------------------------------------------------------------------------------------
In a CI/CD pipeline for Laravel, installing dependencies properly ensures that every developer—especially juniors—can pull the code and run it without hiccups. Here’s how to structure it for consistency and reliability:

# This ensures everyone gets the exact same versions of packages
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader


php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
-------------------------------------------------------

==================== PostgreSQL=================================
CLI:
docker-compose exec postgres psql -U laravel_user -d laravel_db



