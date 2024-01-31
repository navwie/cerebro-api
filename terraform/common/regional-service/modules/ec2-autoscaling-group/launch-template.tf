resource "aws_launch_template" "this" {
  name_prefix = "${var.service_name}-${var.environment}-lt-"

  ebs_optimized = var.ebs_optimized

  image_id = data.aws_ami.debian12_custom.id

  instance_type = "t4g.micro" # default value, may be overrided by ASG

  key_name = aws_key_pair.this.key_name

  block_device_mappings {
    device_name = "/dev/xvda" # root volume

    ebs {
      volume_size           = var.root_volume_size
      delete_on_termination = true
      encrypted             = true
      volume_type           = "gp3"
    }
  }

  metadata_options {
    instance_metadata_tags      = "enabled"
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 2 # to access metadata from docker container
  }

  monitoring {
    enabled = true
  }

  network_interfaces {
    associate_public_ip_address = true
    security_groups             = [aws_security_group.public.id]
  }

  tag_specifications {
    resource_type = "instance"

    tags = {
      Name       = "${var.service_name}-${var.environment}-ag"
      env        = var.environment
      monitoring = true # for prometheus
    }
  }

  iam_instance_profile {
    name = aws_iam_instance_profile.main.name
  }

  update_default_version = true

  user_data = base64encode(templatefile("${path.module}/templates/user_data.txt", local.instance_variables))
}
