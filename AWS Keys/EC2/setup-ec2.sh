#!/bin/bash
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