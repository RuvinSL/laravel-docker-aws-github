#!/bin/bash

set -e

echo "🚀 Starting deployment process..."

# Configuration
AWS_REGION="us-east-1"
ECR_REPOSITORY="your-ecr-repo-name"
ECS_CLUSTER="production-cluster"
ECS_SERVICE="laravel-app-service"

# Get the latest Git commit hash
GIT_COMMIT=$(git rev-parse HEAD)
IMAGE_TAG=${GIT_COMMIT:0:7}

echo "📦 Building Docker image..."
docker build -t ${ECR_REPOSITORY}:${IMAGE_TAG} -f docker/php/Dockerfile .

echo "🔐 Logging into ECR..."
aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REPOSITORY}

echo "📤 Pushing image to ECR..."
docker push ${ECR_REPOSITORY}:${IMAGE_TAG}
docker tag ${ECR_REPOSITORY}:${IMAGE_TAG} ${ECR_REPOSITORY}:latest
docker push ${ECR_REPOSITORY}:latest

echo "🔄 Updating ECS service..."
aws ecs update-service \
    --cluster ${ECS_CLUSTER} \
    --service ${ECS_SERVICE} \
    --force-new-deployment \
    --region ${AWS_REGION}

echo "✅ Deployment completed successfully!"