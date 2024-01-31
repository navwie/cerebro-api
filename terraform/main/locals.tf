locals {
  application        = "dnm"
  service_name       = "${local.application}-api1"
  short_service_name = var.environment == "prod" ? "p-ap1" : "d-ap1" # <= 5 symbols

  secret_name = data.terraform_remote_state.infra.outputs.application_env_secrets_names["api"]

  tags = {
    ManagedBy   = "Terraform"
    Application = local.application
    Owner       = local.application
    GitRepo     = "github.com/DiscoverNimbus2/cerebro-api"
    Environment = var.environment
  }
}
