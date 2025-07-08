terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
  
  backend "s3" {
    bucket         = "your-terraform-state-bucket"
    key            = "production/terraform.tfstate"
    region         = "us-east-1"
    dynamodb_table = "terraform-state-lock"
    encrypt        = true
  }
}

provider "aws" {
  region = var.aws_region
}

module "vpc" {
  source = "../../modules/vpc"
  
  project_name = var.project_name
  environment  = var.environment
  vpc_cidr     = var.vpc_cidr
}

module "rds" {
  source = "../../modules/rds"
  
  project_name        = var.project_name
  environment         = var.environment
  database_name       = var.database_name
  database_username   = var.database_username
  vpc_id             = module.vpc.vpc_id
  private_subnets    = module.vpc.private_subnets
}

module "ecs" {
  source = "../../modules/ecs"
  
  project_name         = var.project_name
  environment          = var.environment
  ecr_repository_url   = var.ecr_repository_url
  vpc_id              = module.vpc.vpc_id
  private_subnets     = module.vpc.private_subnets
  alb_target_group_arn = module.alb.target_group_arn
}