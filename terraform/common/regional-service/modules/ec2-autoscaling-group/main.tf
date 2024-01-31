resource "aws_autoscaling_group" "dev" {
  count = var.environment == "dev" ? 1 : 0

  name_prefix               = "${var.service_name}-${var.environment}-ag-"
  desired_capacity          = var.ag_desired_capacity
  min_size                  = var.ag_min_size
  max_size                  = var.ag_max_size
  default_cooldown          = var.scale_in_cooldown
  default_instance_warmup   = var.default_instance_warmup
  health_check_grace_period = var.health_check_grace_period
  health_check_type         = var.health_check_type
  force_delete              = true
  vpc_zone_identifier       = var.subnets_ids
  target_group_arns         = [aws_lb_target_group.this.arn]
  capacity_rebalance        = var.enable_spot_instance
  termination_policies      = ["OldestLaunchTemplate", "Default"]

  mixed_instances_policy {
    instances_distribution {
      spot_allocation_strategy                 = "price-capacity-optimized"
      on_demand_allocation_strategy            = "prioritized"
      on_demand_percentage_above_base_capacity = var.enable_spot_instance == true ? 0 : 100
    }

    launch_template {
      launch_template_specification {
        launch_template_id = aws_launch_template.this.id
        version            = aws_launch_template.this.latest_version
      }

      dynamic "override" {
        for_each = var.instances

        content {
          instance_type     = override.value.type
          weighted_capacity = override.value.weight
        }
      }
    }
  }

  dynamic "warm_pool" {
    for_each = var.enable_warm_pool == true ? [{}] : []

    content {
      pool_state                  = "Stopped"
      min_size                    = var.warm_pool_min_size
      max_group_prepared_capacity = var.warm_pool_max_size

      instance_reuse_policy {
        reuse_on_scale_in = false
      }
    }
  }

  tag {
    key                 = "service_name"
    value               = "${var.service_name}-${var.environment}"
    propagate_at_launch = true
  }

  timeouts {
    delete = "15m"
  }

  lifecycle {
    ignore_changes = [
      desired_capacity
    ]
  }

  depends_on = [
    aws_launch_template.this
  ]
}

resource "aws_autoscaling_group" "prod" {
  count = var.environment == "prod" ? 1 : 0

  name_prefix               = "${var.service_name}-${var.environment}-ag-"
  desired_capacity          = var.ag_desired_capacity
  min_size                  = var.ag_min_size
  max_size                  = var.ag_max_size
  default_cooldown          = var.scale_in_cooldown
  default_instance_warmup   = var.default_instance_warmup
  health_check_grace_period = var.health_check_grace_period
  health_check_type         = var.health_check_type
  force_delete              = true
  vpc_zone_identifier       = var.subnets_ids
  target_group_arns         = [aws_lb_target_group.this.arn]
  capacity_rebalance        = false
  termination_policies      = ["OldestLaunchTemplate", "Default"]
  enabled_metrics           = var.asg_enabled_metrics

  launch_template {
    id      = aws_launch_template.this.id
    version = aws_launch_template.this.latest_version
  }

  dynamic "warm_pool" {
    for_each = var.enable_warm_pool == true ? [{}] : []

    content {
      pool_state                  = "Stopped"
      min_size                    = var.warm_pool_min_size
      max_group_prepared_capacity = var.warm_pool_max_size

      instance_reuse_policy {
        reuse_on_scale_in = false
      }
    }
  }

  tag {
    key                 = "service_name"
    value               = "${var.service_name}-${var.environment}"
    propagate_at_launch = true
  }

  timeouts {
    delete = "15m"
  }

  lifecycle {
    ignore_changes = [
      desired_capacity
    ]
  }

  depends_on = [
    aws_launch_template.this
  ]
}
