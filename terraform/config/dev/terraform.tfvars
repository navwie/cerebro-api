environment     = "dev"
account_id      = "015542050545"
enabled_regions = ["us-east-1"]

ebs_optimized    = false # isn't supported by t2 instance family
root_volume_size = 15
instances = {
  "instance1" = {
    type   = "t4g.micro"
    weight = "1"
  }
  "instance2" = {
    type   = "t3.micro"
    weight = "1"
  }
  /* "instance3" = {
    type   = "t3.small"
    weight = "2"
  }
  "instance4" = {
    type   = "t2.small"
    weight = "2"
  } */
}

# AG options
default_instance_warmup   = 60
health_check_grace_period = 60
health_check_type         = "EC2"
enable_spot_instance      = true
asg_enabled_metrics       = null

# Autoscaling options
enable_autoscaling                 = true
enable_predictive_autoscaling      = true
predictive_autoscaling_mode        = "ForecastOnly" # ForecastAndScale or ForecastOnly
predictive_autoscaling_buffer_time = 600
ag_desired_capacity                = 1
ag_min_size                        = 1
ag_max_size                        = 2
cpu_target                         = 40
scale_in_cooldown                  = 300

# LB options
container_port     = 8080
host_header_values = ["cerebrohub.smissltd.com"]
healthcheck_path   = "/health"

# Warm pool options
enable_warm_pool   = false
warm_pool_min_size = 1
warm_pool_max_size = 1
