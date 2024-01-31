output "target_group_arn_suffix" {
  value = module.ec2_autoscaling_group.aws_lb_target_group
}

output "asg_prod_name" {
  value = try(module.ec2_autoscaling_group.asg_prod_name, null)
}
