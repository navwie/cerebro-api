resource "aws_autoscaling_policy" "step_scaling_up" {
  enabled                   = var.enable_autoscaling
  name                      = "${var.service_name}-${var.environment}-ag-step-scaling-up-policy"
  policy_type               = "StepScaling"
  autoscaling_group_name    = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name
  estimated_instance_warmup = 60
  adjustment_type           = "ChangeInCapacity"

  step_adjustment {
    scaling_adjustment          = 1
    metric_interval_lower_bound = var.cpu_target
    metric_interval_upper_bound = var.cpu_target * 2
  }

  step_adjustment {
    scaling_adjustment          = 2
    metric_interval_lower_bound = var.cpu_target * 2
  }
}

resource "aws_autoscaling_policy" "step_scaling_down" {
  enabled                = var.enable_autoscaling
  name                   = "${var.service_name}-${var.environment}-ag-step-scaling-down-policy"
  policy_type            = "StepScaling"
  autoscaling_group_name = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name
  adjustment_type        = "ChangeInCapacity"

  step_adjustment {
    scaling_adjustment          = -1
    metric_interval_upper_bound = var.cpu_target * 0.5
  }

  step_adjustment {
    scaling_adjustment          = 0
    metric_interval_lower_bound = var.cpu_target * 0.5
  }
}

resource "aws_autoscaling_policy" "predictive" {
  enabled                = var.enable_predictive_autoscaling
  name                   = "${var.service_name}-${var.environment}-ag-predictive-policy"
  policy_type            = "PredictiveScaling"
  autoscaling_group_name = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name

  predictive_scaling_configuration {
    mode                   = var.predictive_autoscaling_mode
    scheduling_buffer_time = var.predictive_autoscaling_buffer_time

    metric_specification {
      target_value = var.cpu_target

      predefined_metric_pair_specification {
        predefined_metric_type = "ASGCPUUtilization"
        resource_label         = aws_lb_target_group.this.name
      }
    }
  }
}
