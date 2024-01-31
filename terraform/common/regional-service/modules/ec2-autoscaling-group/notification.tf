resource "aws_autoscaling_notification" "this" {
  count = var.environment == "prod" ? 1 : 0

  group_names = [var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name]
  notifications = [
    "autoscaling:EC2_INSTANCE_LAUNCH_ERROR",
    "autoscaling:EC2_INSTANCE_TERMINATE_ERROR",
  ]
  topic_arn = var.topic_arn
}
