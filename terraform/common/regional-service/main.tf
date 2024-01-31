module "ec2_autoscaling_group" {
  source = "./modules/ec2-autoscaling-group"

  environment         = var.environment
  region              = var.region
  tags                = var.tags
  application         = var.application
  service_name        = var.service_name
  short_service_name  = var.short_service_name
  image_tag           = var.image_tag
  container_image_url = var.container_image_url
  container_port      = var.container_port
  ecr_arn             = data.terraform_remote_state.infra.outputs.api_ecr_arn
  s3_arn              = var.environment == "prod" ? data.terraform_remote_state.infra.outputs.general_s3_arn : data.terraform_remote_state.infra.outputs.general_dev_s3_arn
  topic_arn           = data.terraform_remote_state.infra.outputs.warning_sns_asg_topic_arn[var.region]
  vpc_cidr_block      = data.terraform_remote_state.infra.outputs.vpc_cidr_block[var.region]
  secret_name         = var.secret_name[var.environment]
  instances           = var.instances
  root_volume_size    = var.root_volume_size
  ebs_optimized       = var.ebs_optimized
  asg_enabled_metrics = var.asg_enabled_metrics

  # AG options
  default_instance_warmup   = var.default_instance_warmup
  health_check_grace_period = var.health_check_grace_period
  health_check_type         = var.health_check_type
  enable_spot_instance      = var.enable_spot_instance

  # Autoscaling options
  enable_autoscaling                 = var.enable_autoscaling
  enable_predictive_autoscaling      = var.enable_predictive_autoscaling
  predictive_autoscaling_mode        = var.predictive_autoscaling_mode
  predictive_autoscaling_buffer_time = var.predictive_autoscaling_buffer_time
  ag_desired_capacity                = var.ag_desired_capacity
  ag_min_size                        = var.ag_min_size
  ag_max_size                        = var.ag_max_size
  cpu_target                         = var.cpu_target
  scale_in_cooldown                  = var.scale_in_cooldown

  # LB options
  healthcheck_path       = var.healthcheck_path
  subnets_ids            = data.terraform_remote_state.infra.outputs.public_subnets_ids[var.region]
  vpc_id                 = data.terraform_remote_state.infra.outputs.vpc_id[var.region]
  alb_fixed_listener_arn = data.terraform_remote_state.infra.outputs.alb_fixed_listener_arns[var.region]
  host_header_values     = var.host_header_values

  # Warm pool options
  enable_warm_pool   = var.enable_warm_pool
  warm_pool_min_size = var.warm_pool_min_size
  warm_pool_max_size = var.warm_pool_max_size
}

module "alerting" {
  count  = var.environment == "prod" ? 1 : 0
  source = "./modules/alerting"

  environment             = var.environment
  lb_arn_suffix           = data.terraform_remote_state.infra.outputs.alb_arn_suffix[var.region]
  target_group_arn_suffix = module.ec2_autoscaling_group.aws_lb_target_group
  tags                    = var.tags
  application             = var.application
}
