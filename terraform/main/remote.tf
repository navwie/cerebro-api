data "terraform_remote_state" "infra" {
  backend = "s3"

  config = {
    region = "us-east-1"
    bucket = "tf-remote-state20230306132739905500000001"
    key    = "services/application/infra.tfstate"
  }
}
