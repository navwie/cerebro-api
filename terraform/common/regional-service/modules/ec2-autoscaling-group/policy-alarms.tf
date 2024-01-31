resource "aws_cloudwatch_metric_alarm" "asg-alert-scale-up" {
  alarm_name          = "${var.service_name}-${var.environment}-asg-alert-scale-up"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = 1
  threshold           = 0
  alarm_description   = "Alarm for ASG scale up"
  alarm_actions       = [aws_autoscaling_policy.step_scaling_up.arn]

  tags = merge(var.tags, ({
    Name = "${var.service_name}-${var.environment}-asg-alert-scale-up"
  }))

  metric_query {
    id          = "m1"
    return_data = "true"

    metric {
      metric_name = "CPUUtilization"
      namespace   = "AWS/EC2"
      period      = "30"
      stat        = "Average"

      dimensions = {
        AutoScalingGroupName = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name
      }
    }
  }
}

resource "aws_cloudwatch_metric_alarm" "asg-alert-scale-down" {
  alarm_name          = "${var.service_name}-${var.environment}-asg-alert-scale-down"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = 2
  datapoints_to_alarm = 2
  threshold           = 0
  alarm_description   = "Alarm for ASG scale down"
  alarm_actions       = [aws_autoscaling_policy.step_scaling_down.arn]

  tags = merge(var.tags, ({
    Name = "${var.service_name}-${var.environment}-asg-alert-scale-down"
  }))

  metric_query {
    id          = "m1"
    return_data = "true"

    metric {
      metric_name = "CPUUtilization"
      namespace   = "AWS/EC2"
      period      = "300"
      stat        = "Average"

      dimensions = {
        AutoScalingGroupName = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name
      }
    }
  }
}
