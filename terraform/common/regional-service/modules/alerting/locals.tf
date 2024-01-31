locals {
  alarm_actions = {
    all    = [aws_sns_topic.warning_sns_alb_devops_topic.arn, aws_sns_topic.warning_sns_alb_dev_topic.arn]
    devops = [aws_sns_topic.warning_sns_alb_devops_topic.arn]
    dev    = [aws_sns_topic.warning_sns_alb_dev_topic.arn]
  }
  alarm_prefix = "${var.application}-api-${var.environment}-alb"
  thresholds = {
    error_rate     = 1
    latency_10sec  = 50
    error_rate_5xx = 20
  }
  email = {
    dev = [
      "artem.lashyn@smissltd.com",
      "rostyslav.kozel@smissltd.com",
      "vladyslav.korneenkov@smissltd.com",
    ]
    devops = [
      "kyryl.tsvyntarnyi@smissltd.com",
      "denis.rondalev@smissltd.com",
      "mykhailo.lavrentiev@smissltd.com",
    ]
  }
}
