variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "us-east-1"
}

variable "project_name" {
  description = "Project name"
  type        = string
  default     = "laravel-app"
}

variable "environment" {
  description = "Environment name"
  type        = string
  default     = "production"
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "database_name" {
  description = "RDS database name"
  type        = string
  default     = "laravel"
}

variable "database_username" {
  description = "RDS database username"
  type        = string
  default     = "admin"
}

variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.medium"
}

variable "ecr_repository_url" {
  description = "ECR repository URL"
  type        = string
}

variable "task_cpu" {
  description = "ECS task CPU units"
  type        = string
  default     = "512"
}

variable "task_memory" {
  description = "ECS task memory"
  type        = string
  default     = "1024"
}

variable "app_count" {
  description = "Number of app instances"
  type        = number
  default     = 3
}

variable "certificate_arn" {
  description = "ACM certificate ARN for HTTPS"
  type        = string
}

variable "allowed_origins" {
  description = "Allowed CORS origins"
  type        = list(string)
  default     = ["https://example.com"]
}