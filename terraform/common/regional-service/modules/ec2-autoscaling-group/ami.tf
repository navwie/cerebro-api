data "aws_ami" "debian12_custom" {
  most_recent = true

  filter {
    name   = "name"
    values = ["debian12-arm64-*"]
  }

  filter {
    name   = "architecture"
    values = ["arm64"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = ["015542050545"] # DNM Account ID
}
