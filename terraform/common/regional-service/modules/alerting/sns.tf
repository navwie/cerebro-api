# SNS Topic
resource "aws_sns_topic" "warning_sns_alb_devops_topic" {
  name = "${var.application}-cerebro-api-devops-alert-sns-topic"
  tags = merge(var.tags, ({
    Name = "${var.application}-sns_devops_warning_topic"
  }))
}

resource "aws_sns_topic_subscription" "warning_topic_devops_email_subscription" {
  count     = length(local.email.devops)
  topic_arn = aws_sns_topic.warning_sns_alb_devops_topic.arn
  protocol  = "email"
  endpoint  = local.email.devops[count.index]
}

resource "aws_sns_topic" "warning_sns_alb_dev_topic" {
  name = "${var.application}-cerebro-api-dev-alert-sns-topic"
  tags = merge(var.tags, ({
    Name = "${var.application}-sns_dev_warning_topic"
  }))
}

resource "aws_sns_topic_subscription" "warning_topic_dev_email_subscription" {
  count     = length(local.email.dev)
  topic_arn = aws_sns_topic.warning_sns_alb_dev_topic.arn
  protocol  = "email"
  endpoint  = local.email.dev[count.index]
}
