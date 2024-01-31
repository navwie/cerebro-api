resource "aws_secretsmanager_secret" "multiregional" {
  name = "${var.service_name}-${var.environment}-ag-ssh-key"
  tags = var.tags
}

resource "aws_secretsmanager_secret_version" "multiregional" {
  secret_id     = aws_secretsmanager_secret.multiregional.id
  secret_string = tls_private_key.this.private_key_openssh
}
