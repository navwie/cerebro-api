module "regional_service_eu_central_1" {
  count  = contains(var.enabled_regions, "eu-central-1") ? 1 : 0
  source = "../common/regional-service"

  tags                               = local.tags
  region                             = "eu-central-1"
  account_id                         = var.account_id
  environment                        = var.environment
  application                        = local.application
  service_name                       = local.service_name
  short_service_name                 = local.short_service_name
  healthcheck_path                   = var.healthcheck_path
  host_header_values                 = var.host_header_values
  enable_autoscaling                 = var.enable_autoscaling
  enable_predictive_autoscaling      = var.enable_predictive_autoscaling
  predictive_autoscaling_mode        = var.predictive_autoscaling_mode
  predictive_autoscaling_buffer_time = var.predictive_autoscaling_buffer_time
  ag_desired_capacity                = var.ag_desired_capacity
  ag_min_size                        = var.ag_min_size
  ag_max_size                        = var.ag_max_size
  cpu_target                         = var.cpu_target
  scale_in_cooldown                  = var.scale_in_cooldown
  image_tag                          = var.image_tag
  container_image_url                = var.container_image_url
  container_port                     = var.container_port
  secret_name                        = local.secret_name
  instances                          = var.instances
  root_volume_size                   = var.root_volume_size
  enable_warm_pool                   = var.enable_warm_pool
  warm_pool_min_size                 = var.warm_pool_min_size
  warm_pool_max_size                 = var.warm_pool_max_size
  default_instance_warmup            = var.default_instance_warmup
  health_check_grace_period          = var.health_check_grace_period
  health_check_type                  = var.health_check_type
  enable_spot_instance               = var.enable_spot_instance
  ebs_optimized                      = var.ebs_optimized
  asg_enabled_metrics                = var.asg_enabled_metrics

  providers = {
    aws = aws.eu-central-1
  }
}

module "regional_service_us_east_1" {
  count  = contains(var.enabled_regions, "us-east-1") ? 1 : 0
  source = "../common/regional-service"

  tags                               = local.tags
  region                             = "us-east-1"
  account_id                         = var.account_id
  environment                        = var.environment
  application                        = local.application
  service_name                       = local.service_name
  short_service_name                 = local.short_service_name
  healthcheck_path                   = var.healthcheck_path
  host_header_values                 = var.host_header_values
  enable_autoscaling                 = var.enable_autoscaling
  enable_predictive_autoscaling      = var.enable_predictive_autoscaling
  predictive_autoscaling_mode        = var.predictive_autoscaling_mode
  predictive_autoscaling_buffer_time = var.predictive_autoscaling_buffer_time
  ag_desired_capacity                = var.ag_desired_capacity
  ag_min_size                        = var.ag_min_size
  ag_max_size                        = var.ag_max_size
  cpu_target                         = var.cpu_target
  scale_in_cooldown                  = var.scale_in_cooldown
  image_tag                          = var.image_tag
  container_image_url                = var.container_image_url
  container_port                     = var.container_port
  secret_name                        = local.secret_name
  instances                          = var.instances
  root_volume_size                   = var.root_volume_size
  enable_warm_pool                   = var.enable_warm_pool
  warm_pool_min_size                 = var.warm_pool_min_size
  warm_pool_max_size                 = var.warm_pool_max_size
  default_instance_warmup            = var.default_instance_warmup
  health_check_grace_period          = var.health_check_grace_period
  health_check_type                  = var.health_check_type
  enable_spot_instance               = var.enable_spot_instance
  ebs_optimized                      = var.ebs_optimized
  asg_enabled_metrics                = var.asg_enabled_metrics

  providers = {
    aws = aws.us-east-1
  }
}
