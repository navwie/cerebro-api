variable "environment" {
  type = string
}

variable "account_id" {
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

variable "container_image_url" {
  type        = string
  description = "ECR container image url from GHA outputs"
}

variable "image_tag" {
  type        = string
  description = "ECR container image tag from GHA outputs"
}

variable "enabled_regions" {
  type = list(string)
}

variable "healthcheck_path" {
  type = string
}

variable "host_header_values" {
  type = list(string)
}

variable "container_port" {
  type = number
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
  description = "Enables autoscaling in Auto Scaling Group"
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
