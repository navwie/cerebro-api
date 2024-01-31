variable "vpc_id" {
  type = string
}

variable "application" {
  type = string
}

variable "service_name" {
  type = string
}

variable "short_service_name" {
  type = string
}

variable "instances" {
  type = map(any)
}

variable "root_volume_size" {
  type = number
}

variable "ebs_optimized" {
  type = bool
}

variable "subnets_ids" {
  type = list(string)
}

variable "environment" {
  type        = string
  description = "Environment such as (dev, stage, prod)"
}

variable "vpc_cidr_block" {
  type = string
}

variable "region" {
  type        = string
  description = "AWS region such as (eu-central-1, us-west-1)"
}

variable "tags" {
  type        = map(string)
  description = "Resource tags"
}

variable "healthcheck_path" {
  type = string
}

variable "host_header_values" {
  type = list(string)
}

variable "alb_fixed_listener_arn" {
  type = string
}

variable "ecr_arn" {
  type        = string
  description = ""
}

variable "custom_user_data" {
  description = "Custom user data to be appended to preconfigured one (without Shebang)"
  type        = string
  default     = ""
}

variable "container_port" {
  type = number
}

variable "container_image_url" {
  type        = string
  description = "ECR container image url from GHA outputs"
}

variable "image_tag" {
  type        = string
  description = "ECR container image tag from GHA outputs"
}

# AG options
variable "default_instance_warmup" {
  type = number
}

variable "health_check_grace_period" {
  type        = number
  description = "Time after instance comes into service before checking health"
}

variable "health_check_type" {
  type = string
}

variable "enable_spot_instance" {
  type = bool
}

# Auto scaling options
variable "enable_autoscaling" {
  type        = bool
  description = "Enables EC2 autoscaling"
}

variable "enable_predictive_autoscaling" {
  type        = bool
  description = "Enables Predictive autoscaling in Auto Scaling Group"
}

variable "predictive_autoscaling_mode" {
  type = string
}

variable "predictive_autoscaling_buffer_time" {
  type = number
}

variable "ag_max_size" {
  type        = number
  description = "Maximum size of the Auto Scaling Group"
}

variable "ag_min_size" {
  type        = number
  description = "Minimum size of the Auto Scaling Group"
}

variable "ag_desired_capacity" {
  type        = number
  description = "The number of Amazon EC2 instances that should be running in the group"
}

variable "cpu_target" {
  type        = number
  description = "Target CPU utilisation (%) above which the Auto Scaling Group will scale out"
}

variable "scale_in_cooldown" {
  type        = number
  description = "Amount of time in seconds scaling in will be paused, before the next scale in activity can start."
}

variable "topic_arn" {
  type = string
}

# S3 bucket vars
variable "s3_arn" {
  type        = string
  description = "S3 bucket ARN"
}

variable "secret_name" {
  type        = string
  description = "Application env secret names from AWS secret manager"
}

# Warm pool options
variable "enable_warm_pool" {
  type        = bool
  description = "Enables Warm pool in Auto Scaling Group"
}

variable "warm_pool_min_size" {
  type        = number
  description = "Minimum number of instances to maintain in the warm pool"
}

variable "warm_pool_max_size" {
  type        = number
  description = "Maximum number of instances to maintain in the warm pool"
}

variable "asg_enabled_metrics" {
  type = list(string)
}
