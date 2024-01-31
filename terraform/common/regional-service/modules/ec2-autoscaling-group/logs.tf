resource "aws_cloudwatch_log_group" "ag" {
  name              = "${var.service_name}-${var.environment}-log-group"
  retention_in_days = 7

  tags = merge(var.tags, ({
    Name = "${var.service_name}"
  }))

}
