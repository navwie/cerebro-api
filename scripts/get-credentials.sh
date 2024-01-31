# https://docs.aws.amazon.com/IAM/latest/UserGuide/id_roles_use_switch-role-ec2.html
# https://docs.aws.amazon.com/IAM/latest/UserGuide/id_credentials_access-keys.html

# we can get this value from .env
AWS_EC2_ROLE_NAME="dnm-api-ag-main-us-east-1-dev"

# get token + security credentials for role
AWS_TOKEN=$(curl -s -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 21600") &&
    curl -H "X-aws-ec2-metadata-token: $AWS_TOKEN" -v http://169.254.169.254/latest/meta-data/iam/security-credentials/$AWS_EC2_ROLE_NAME
