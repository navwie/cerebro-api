output "target_group_arn_suffix" {
  value = module.regional_service_us_east_1[0].target_group_arn_suffix
}

output "asg_prod_name" {
  value = try(module.regional_service_us_east_1[0].asg_prod_name, null)
}
