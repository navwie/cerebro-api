resource "tls_private_key" "this" {
  algorithm = "ED25519"
}

resource "aws_key_pair" "this" {
  key_name   = "${var.service_name}-${var.environment}-ag-keypair"
  public_key = tls_private_key.this.public_key_openssh

  tags = var.tags
}
