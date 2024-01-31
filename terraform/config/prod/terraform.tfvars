environment     = "prod"
account_id      = "015542050545"
enabled_regions = ["us-east-1"]

ebs_optimized    = true
root_volume_size = 20
instances = {
  "instance1" = {
    type   = "t4g.micro"
    weight = "1"
  }
}

# AG options
default_instance_warmup   = 60
health_check_grace_period = 60
health_check_type         = "ELB"
enable_spot_instance      = false
asg_enabled_metrics = [
  "GroupInServiceInstances",
]

# Autoscaling options
enable_autoscaling                 = true
enable_predictive_autoscaling      = true
predictive_autoscaling_mode        = "ForecastAndScale" # ForecastAndScale or ForecastOnly
predictive_autoscaling_buffer_time = 600
ag_desired_capacity                = 2
ag_min_size                        = 2
ag_max_size                        = 6
cpu_target                         = 30
scale_in_cooldown                  = 300

# LB options
container_port     = 8080
host_header_values = ["cerebrohub.com"]
healthcheck_path   = "/health"

# Warm pool options
enable_warm_pool   = true
warm_pool_min_size = 1
warm_pool_max_size = 1
