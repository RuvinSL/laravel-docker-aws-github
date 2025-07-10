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



----------------------- DOCKER HUB--------------------------------

Step 1: Docker Hub Setup

Create Docker Hub Account (if not already done)
https://hub.docker.com/signup

Create Repository

Go to: https://hub.docker.com/repositories
Click "Create Repository"
Repository name: laravel-app
Visibility: Private (1 free private repo)
Click "Create"


Generate Access Token
Go to: docker hub > Account Settings → Security
Click "New Access Token"
Description: github-actions
Access permissions: Read, Write, Delete
Click "Generate"
Copy the token immediately (shown only once)

To use the access token from your Docker CLI client:
1. Run
docker login -u ruvinroshan
2. At the password prompt, enter the personal access token.



Step 2: AWS EC2 Setup
2.1 Launch EC2 Instance
bash# Using AWS Console (easier for free tier)
1. Go to EC2 Dashboard
2. Click "Launch Instance"
3. Choose:
   - Name: laravel-staging
   - AMI: Ubuntu Server 22.04 LTS (Free tier eligible)
   - Instance type: t2.micro (Free tier eligible)
   - Key pair: Create new or use existing
   - Network settings:
     - Allow SSH (22)
     - Allow HTTP (80)
     - Allow HTTPS (443)
   - Storage: 8 GB gp2 (Free tier includes 30 GB)
4. Launch instance





2.2 Configure EC2 Instance

Step 1: Save the Script Locally
First, create a file called setup-ec2.sh on your local machine:

Step 2: permissions on your private key file are too open, making it insecure for SSH to use. Here's how to fix it:
For Windows:
    Right-click the .pem file and select Properties.
    Go to the Security tab.
    Click Advanced.
    Click Disable inheritance and choose Remove all inherited permissions.
    Click Add, then Select a principal.
    Type your Windows username and click Check Names, then OK.
    Grant Full control only to your user.
    Click OK to apply changes.

For Linux/macOS:
Run this command to restrict permissions:
chmod 400 laravel-staging-private.pem


Step 3: Check the SSH Username
    For Amazon Linux 2 / Amazon Linux AMI, the default username is ec2-user.
    For Ubuntu, it's ubuntu.  (eg: ssh -i laravel-staging-private.pem ubuntu@54.85.212.101)
    For CentOS, it's centos.
    For RHEL (Red Hat), it's ec2-user.  


Step 4: SSH into Your EC2 Instance
Replace your-key.pem with your actual PEM file and <ec2-user>@<your-ec2-ip> with your EC2 user and public IP:

ssh -i your-key.pem <ec2-user>@<your-ec2-ip>
eg: ssh -i laravel-staging-private.pem ubuntu@54.85.212.101

Step 5: Upload the Script to EC2 (Optional)
If you created the script on your local machine and want to run it on EC2, upload it using scp:

scp -i your-key.pem setup-ec2.sh ec2-user@<your-ec2-ip>:~/
eg: scp -i laravel-staging-private.pem setup-ec2.sh ubuntu@54.85.212.101:~/

Step 6: Run the Script on the EC2 Instance
After uploaded the sh file Make the script executable:
chmod +x setup-ec2.sh

Step 7: Run it
./setup-ec2.sh




######## below is the content of 'sh' file: setup-ec2.sh
SSH into your EC2 instance and run:
bash#!/bin/bash
# save as setup-ec2.sh and run on EC2

# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker ubuntu
newgrp docker

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Create required directories
mkdir -p /home/ubuntu/laravel-storage/app/public
mkdir -p /home/ubuntu/laravel-storage/framework/{cache,sessions,views}
mkdir -p /home/ubuntu/laravel-storage/logs

# Set permissions
sudo chown -R ubuntu:ubuntu /home/ubuntu/laravel-storage
chmod -R 775 /home/ubuntu/laravel-storage

# Create Docker network
docker network create laravel-network

# Create .env.staging file
cat > /home/ubuntu/.env.staging << 'EOF'
APP_NAME=Laravel
APP_ENV=staging
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=http://your-ec2-public-dns.amazonaws.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=laravel-mysql
DB_PORT=3306
DB_DATABASE=laravel_staging
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=laravel-redis
REDIS_PASSWORD=null
REDIS_PORT=6379
EOF

# Run MySQL container
docker run -d \
  --name laravel-mysql \
  --restart unless-stopped \
  --network laravel-network \
  -e MYSQL_ROOT_PASSWORD=root_password \
  -e MYSQL_DATABASE=laravel_staging \
  -e MYSQL_USER=laravel \
  -e MYSQL_PASSWORD=your_secure_password \
  -v mysql_data:/var/lib/mysql \
  mysql:8.0

# Run Redis container
docker run -d \
  --name laravel-redis \
  --restart unless-stopped \
  --network laravel-network \
  redis:alpine

echo "EC2 setup complete!"
#################################################





Step 3: GitHub Secrets Configuration
Go to your GitHub repository → Settings → Secrets and variables → Actions
Add these secrets:
yaml# Docker Hub Credentials
DOCKER_USERNAME: your-dockerhub-username
DOCKER_PASSWORD: your-access-token-from-step-1

# AWS EC2 Access
EC2_STAGING_HOST: your-ec2-public-ip-or-dns
EC2_USERNAME: ubuntu
EC2_SSH_KEY: |
  -----BEGIN RSA PRIVATE KEY-----
  (paste your entire private key here)
  -----END RSA PRIVATE KEY-----

# Optional but recommended
SLACK_WEBHOOK: your-slack-webhook-url






