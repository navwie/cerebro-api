resource "aws_cloudwatch_metric_alarm" "error_rate_alarm" {
  alarm_name          = "${local.alarm_prefix}-target-group-error-rate-warning"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = "5"
  datapoints_to_alarm = "3"
  threshold           = local.thresholds.error_rate
  alarm_description   = "Request error rate has exceeded ${local.thresholds.error_rate}%"
  treat_missing_data  = "notBreaching"
  alarm_actions       = local.alarm_actions.all
  ok_actions          = local.alarm_actions.all

  tags = merge(var.tags, ({
    Name = "${local.alarm_prefix}-error-rate"
  }))

  /* metric_query {
    id = "target4xxCount"

    metric {
      metric_name = "HTTPCode_Target_4XX_Count"
      namespace   = "AWS/ApplicationELB"
      period      = "300"
      stat        = "Sum"

      dimensions = {
        LoadBalancer = var.lb_arn_suffix
        TargetGroup  = var.target_group_arn_suffix
      }
    }
  } */

  metric_query {
    id = "target5xxCount"

    metric {
      metric_name = "HTTPCode_Target_5XX_Count"
      namespace   = "AWS/ApplicationELB"
      period      = "300"
      stat        = "Sum"

      dimensions = {
        LoadBalancer = var.lb_arn_suffix
        TargetGroup  = var.target_group_arn_suffix
      }
    }
  }

  metric_query {
    id = "requestCount"

    metric {
      metric_name = "RequestCount"
      namespace   = "AWS/ApplicationELB"
      period      = "300"
      stat        = "Sum"

      dimensions = {
        LoadBalancer = var.lb_arn_suffix
        TargetGroup  = var.target_group_arn_suffix
      }
    }
  }

  metric_query {
    id = "failureRate"
    /* expression  = "((target4xxCount + target5xxCount) / requestCount) * 100" */
    expression  = "(target5xxCount / requestCount) * 100"
    label       = "Error Rate"
    return_data = true
    period      = 300
  }

}

resource "aws_cloudwatch_metric_alarm" "latency_10sec_alarm" {
  alarm_name          = "${local.alarm_prefix}-high-latency-warning"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = "9"
  datapoints_to_alarm = "5"
  threshold           = 10 // 10 sec
  alarm_description   = "Target response time has exeeded 10sec for more than ${100 - local.thresholds.latency_10sec}% of requests."
  treat_missing_data  = "notBreaching"
  alarm_actions       = local.alarm_actions.devops
  ok_actions          = local.alarm_actions.devops

  tags = merge(var.tags, ({
    Name = "${local.alarm_prefix}-error-rate"
  }))

  metric_query {
    id          = "m1"
    return_data = "true"

    metric {
      metric_name = "TargetResponseTime"
      namespace   = "AWS/ApplicationELB"
      period      = "120"
      stat        = "p${local.thresholds.latency_10sec}"
      unit        = "Seconds"

      dimensions = {
        LoadBalancer = var.lb_arn_suffix
        TargetGroup  = var.target_group_arn_suffix
      }
    }
  }
}

resource "aws_cloudwatch_metric_alarm" "unhealthy-host-count" {
  alarm_name          = "${local.alarm_prefix}-unhealthy-host-count"
  alarm_description   = "Alert when ELB HealthCheck registers unhealthy host"
  metric_name         = "UnHealthyHostCount"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  threshold           = "1"
  evaluation_periods  = "3"
  datapoints_to_alarm = "2"
  alarm_actions       = local.alarm_actions.devops
  namespace           = "AWS/ApplicationELB"
  period              = "60"
  statistic           = "Average"

  tags = merge(var.tags, ({
    Name = "${local.alarm_prefix}-error-rate"
  }))

  dimensions = {
    LoadBalancer = var.lb_arn_suffix
    TargetGroup  = var.target_group_arn_suffix
  }
}

resource "aws_cloudwatch_metric_alarm" "error-rate-5xx-warning" {
  alarm_name          = "${local.alarm_prefix}-5xx-error-rate-warning"
  alarm_description   = "Alert when 5xx error rate more than ${local.thresholds.error_rate_5xx}"
  metric_name         = "HTTPCode_Target_5XX_Count"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  threshold           = local.thresholds.error_rate_5xx
  evaluation_periods  = 1
  alarm_actions       = local.alarm_actions.all
  namespace           = "AWS/ApplicationELB"
  period              = "86400"
  statistic           = "Sum"

  tags = merge(var.tags, ({
    Name = "${local.alarm_prefix}-5xx-error-rate"
  }))

  dimensions = {
    LoadBalancer = var.lb_arn_suffix
    TargetGroup  = var.target_group_arn_suffix
  }
}
