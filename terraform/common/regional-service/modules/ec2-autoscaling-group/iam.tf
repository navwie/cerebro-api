data "aws_iam_policy_document" "ag_main_role" {
  statement {
    actions = ["sts:AssumeRole"]
    effect  = "Allow"

    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

resource "aws_iam_role" "ag_main_role" {
  name               = "${var.service_name}-${var.environment}-ag-main-${var.region}"
  assume_role_policy = data.aws_iam_policy_document.ag_main_role.json

  tags = merge(var.tags, ({
    Name = "${var.service_name}-${var.environment}-ag-main-${var.region}"
  }))
}

data "aws_iam_policy_document" "ag_main_policy" {
  statement {
    sid = "S3Access"

    resources = [
      var.s3_arn,
      "${var.s3_arn}/*",
    ]

    actions = ["s3:*"]
  }

  statement {
    sid = "testS3Access"

    resources = [
      "arn:aws:s3:::dnm-test-logs",
      "arn:aws:s3:::dnm-test-logs/*",
    ]

    actions = ["s3:*"]
  }

  statement {
    sid       = "GetAuthorizationTokenForAllECR"
    resources = ["*"]

    actions = [
      "ecr:GetAuthorizationToken",
    ]
  }

  statement {
    sid       = "GetSsmParameters"
    resources = ["*"]

    actions = [
      "ssm:GetParameters",
    ]
  }

  statement {
    sid       = "CloudWatchAgentAdminLogs"
    resources = ["*"]

    actions = [
      "cloudwatch:PutMetricData",
      "ec2:DescribeTags",
      "logs:PutLogEvents",
      "logs:DescribeLogStreams",
      "logs:DescribeLogGroups",
      "logs:CreateLogStream",
      "logs:CreateLogGroup"
    ]
  }

  statement {
    sid       = "CloudWatchAgentAdminSsm"
    resources = ["arn:aws:ssm:*:*:parameter/AmazonCloudWatch-*"]

    actions = [
      "ssm:GetParameter",
      "ssm:PutParameter"
    ]
  }

  statement {
    sid       = "CloudWatchLogsOperations"
    resources = ["*"]

    actions = [
      "logs:*",
    ]
  }

  statement {
    sid       = "WritePermissionsForECR"
    resources = [var.ecr_arn]

    actions = [
      "ecr:BatchCheckLayerAvailability",
      "ecr:GetDownloadUrlForLayer",
      "ecr:GetRepositoryPolicy",
      "ecr:DescribeRepositories",
      "ecr:ListImages",
      "ecr:DescribeImages",
      "ecr:BatchGetImage",
      "ecr:GetLifecyclePolicy",
      "ecr:GetLifecyclePolicyPreview",
      "ecr:ListTagsForResource",
      "ecr:DescribeImageScanFindings"
    ]
  }

  statement {
    sid       = "GetAllAsgData"
    resources = ["*"]

    actions = [
      "autoscaling:DescribeAutoScalingInstances",
      "autoscaling:DescribeAutoScalingGroups"
    ]
  }

  statement {
    sid       = "RestrictAsgInstanceTermination"
    resources = [var.environment == "prod" ? aws_autoscaling_group.prod[0].arn : aws_autoscaling_group.dev[0].arn]

    actions = [
      "autoscaling:SetInstanceProtection"
    ]
  }

  statement {
    sid       = "CompleteLifecycleAction"
    resources = [var.environment == "prod" ? aws_autoscaling_group.prod[0].arn : aws_autoscaling_group.dev[0].arn]

    actions = [
      "autoscaling:CompleteLifecycleAction"
    ]
  }

  statement {
    sid       = "GetAllSecretValue"
    resources = ["*"]

    actions = [
      "secretsmanager:GetSecretValue"
    ]
  }
}

resource "aws_iam_policy" "ag_main_policy" {
  name   = "${var.service_name}-${var.environment}-ag-main-${var.region}"
  policy = data.aws_iam_policy_document.ag_main_policy.json

  tags = merge(var.tags, ({
    Name = "${var.service_name}-${var.environment}-ag-main-${var.region}"
  }))
}

resource "aws_iam_role_policy_attachment" "ag_main" {
  role       = aws_iam_role.ag_main_role.id
  policy_arn = aws_iam_policy.ag_main_policy.arn
}

resource "aws_iam_instance_profile" "main" {
  name = "${var.service_name}-${var.environment}-ag-main-instance-profile"
  role = aws_iam_role.ag_main_role.name
}
