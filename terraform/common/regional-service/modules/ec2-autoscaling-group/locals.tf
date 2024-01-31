locals {
  instance_variables = {
    CONTAINER_IMAGE_URL = var.container_image_url
    IMAGE_TAG           = var.image_tag
    APP_PORT            = var.container_port
    AWS_DEFAULT_REGION  = var.region
    AWS_LOG_GROUP       = aws_cloudwatch_log_group.ag.name
    SECRET_NAME         = var.secret_name
    ENVIRONMENT         = var.environment
  }
}
