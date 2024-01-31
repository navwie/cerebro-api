output "aws_lb_target_group" {
  value = aws_lb_target_group.this.arn_suffix
}

output "asg_prod_name" {
  value = try(aws_autoscaling_group.prod[0].name, null)
}
