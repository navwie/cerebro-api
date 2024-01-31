variable "tags" {
  type = map(string)
}

variable "application" {
  type = string
}

variable "environment" {
  type = string
}

variable "lb_arn_suffix" {
  type = string
}

variable "target_group_arn_suffix" {
  type = string
}
