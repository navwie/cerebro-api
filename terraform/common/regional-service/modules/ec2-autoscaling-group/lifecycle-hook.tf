resource "aws_autoscaling_lifecycle_hook" "init" {
  name                   = "init"
  autoscaling_group_name = var.environment == "prod" ? aws_autoscaling_group.prod[0].name : aws_autoscaling_group.dev[0].name
  default_result         = "CONTINUE"
  heartbeat_timeout      = 300
  lifecycle_transition   = "autoscaling:EC2_INSTANCE_LAUNCHING"

  # notification_metadata = jsonencode({
  #   foo = "this"
  # })

  # notification_target_arn = "arn:aws:sqs:us-east-1:444455556666:queue1*"
  # role_arn                = "arn:aws:iam::123456789012:role/S3Access"
}
