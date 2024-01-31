resource "aws_lb_target_group" "this" {
  name_prefix = "${var.short_service_name}-"
  port        = var.container_port
  protocol    = "HTTP"
  vpc_id      = var.vpc_id
  target_type = "instance"

  stickiness {
    enabled = true
    type    = "lb_cookie"
  }

  health_check {
    path                = var.healthcheck_path
    matcher             = "200-302"
    protocol            = "HTTP"
    healthy_threshold   = 2
    unhealthy_threshold = 3
    timeout             = 30
    interval            = 60
  }

  lifecycle {
    create_before_destroy = true
  }

  tags = merge(var.tags, ({
    Name = "${var.application}-${var.environment}-ag-target-group"
  }))
}

resource "aws_lb_listener_rule" "rule1" {
  count = length(var.host_header_values) >= 1 ? 1 : 0

  listener_arn = var.alb_fixed_listener_arn

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.this.arn
  }

  condition {
    path_pattern {
      values = ["/*"]
    }
  }

  condition {
    host_header {
      values = var.host_header_values
    }
  }

  lifecycle {
    create_before_destroy = true
  }

  tags = merge(var.tags, ({
    Name = "${var.application}-${var.environment}-listener-rule1"
  }))
}
