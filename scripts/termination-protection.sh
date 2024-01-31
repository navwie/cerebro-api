# get token + instance id + region + auto-scaling group name from metadata
AWS_TOKEN=$(curl -s -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 21600") &&
    AWS_INSTANCE_ID=$(curl -s -H "X-aws-ec2-metadata-token: $AWS_TOKEN" http://169.254.169.254/latest/meta-data/instance-id) &&
    AWS_REGION=$(curl -s -H "X-aws-ec2-metadata-token: $AWS_TOKEN" http://169.254.169.254/latest/dynamic/instance-identity/document | jq -r .region) &&
    AWS_ASG_NAME=$(aws autoscaling describe-auto-scaling-instances --instance-ids=$AWS_INSTANCE_ID --region $AWS_REGION)

# enable termination protection
aws autoscaling set-instance-protection --instance-ids $AWS_INSTANCE_ID --auto-scaling-group-name $AWS_ASG_NAME --protected-from-scale-in --region $AWS_REGION

# disable termination protection
aws autoscaling set-instance-protection --instance-ids $AWS_INSTANCE_ID --auto-scaling-group-name $AWS_ASG_NAME --no-protected-from-scale-in --region $AWS_REGION
