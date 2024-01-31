#!/bin/bash

print_error() {
  printf '%sERROR: %s%s\n' "$(printf '\033[31m')" "$*" "$(printf '\033[m')" >&2
  exit 1
}

while getopts "m:n:c:e:g:" option; do
  case "${option}" in
  m) debug_mode=${OPTARG} ;;
  n) as_full_name=${OPTARG} ;;
  c) as_capacity=${OPTARG} ;;
  e) env=${OPTARG} ;;
  g) as_prefix=${OPTARG} ;;
  esac
done

echo "########### START AT $(date) ###########"

if [ $as_capacity -gt 1 ]; then

  echo "########### START INSTANCE REFRESH AT $(date) ###########"
  REFRESH_ID=$(aws autoscaling start-instance-refresh --auto-scaling-group-name $as_full_name --preference '{"MinHealthyPercentage":100,"SkipMatching":false}' | jq -r '.InstanceRefreshId')

  if [ "$debug_mode" == "true" ]; then
    attempt_timeout=15
    refresh_status=$(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].Status')

    while [ "$refresh_status" != "Successful" ]; do
      refresh_status=$(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].Status')

      if [[ "$refresh_status" == "InProgress" || "$refresh_status" == "Pending" ]]; then
        echo "Instance refresh in progress
        Start time: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].StartTime')
        Status reason: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].StatusReason')
        Percentage complete: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].PercentageComplete')
        "
        sleep $attempt_timeout
      elif [[ "$refresh_status" == "Successful" ]]; then
        echo "Instance refresh complited successfuly
        End time: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].EndTime')
        "
        break

      else
        echo "Instance refresh is $refresh_status"
        break

      fi
    done
  fi

else

  echo "########### DESIRED CAPACITY INCREASING STARTED AT $(date) ###########"
  aws autoscaling update-auto-scaling-group --auto-scaling-group-name $as_full_name --desired-capacity 2

  max_retry=5
  attempt_timeout=5

  for i in $(seq 1 $max_retry); do

    instance_count=$(aws autoscaling describe-auto-scaling-groups --filter Name=tag-value,Values="$as_prefix-$env" | jq -r '.AutoScalingGroups[].Instances[].InstanceId' | sed -n '$=')

    if [[ $instance_count -gt 1 ]]; then
      echo "########### DESIRED CAPACITY INCREASED SUCCESSFULY AT $(date) ###########"
      if [ "$debug_mode" == "true" ]; then
        echo "########### ASG CONFIGURATION ###########"
        aws autoscaling describe-auto-scaling-groups --filter Name=tag-value,Values="$as_prefix-$env" | jq -r '.AutoScalingGroups[]'
        echo "########### AUTOSCALLING ACTIVITE  ###########"
        aws autoscaling describe-scaling-activities --auto-scaling-group-name $as_full_name --max-items 5
        aws autoscaling describe-auto-scaling-groups --filter Name=tag-value,Values="$as_prefix-$env" | jq -r '.AutoScalingGroups[]'
      fi
      status="success"
      break

    else
      echo "########### DESIRED CAPACITY DOESN'T INCREASED - RETRY AT $(date) ###########"
      if [ "$debug_mode" == "true" ]; then
        echo "########### ASG CONFIGURATION ###########"
        aws autoscaling describe-auto-scaling-groups --filter Name=tag-value,Values="$as_prefix-$env" | jq -r '.AutoScalingGroups[]'
        echo "########### AUTOSCALLING ACTIVITE  ###########"
        aws autoscaling describe-scaling-activities --auto-scaling-group-name $as_full_name --max-items 5
        aws autoscaling describe-auto-scaling-groups --filter Name=tag-value,Values="$as_prefix-$env" | jq -r '.AutoScalingGroups[]'
      fi
      aws autoscaling update-auto-scaling-group --auto-scaling-group-name $as_full_name --desired-capacity 2
      sleep $attempt_timeout
      status="failure"
    fi

  done

  if [ "$status" != "success" ]; then
    print_error "Desired capacity increasing was failured, try to resolve issue and restart refresh. 
    $(aws autoscaling describe-scaling-activities --auto-scaling-group-name $as_full_name --max-items 1)"
  fi

  echo "########### START INSTANCE REFRESH AT $(date) ###########"
  REFRESH_ID=$(aws autoscaling start-instance-refresh --auto-scaling-group-name $as_full_name --preference '{"MinHealthyPercentage":100,"SkipMatching":false}' | jq -r '.InstanceRefreshId')

  if [ "$debug_mode" == "true" ]; then
    attempt_timeout=15
    refresh_status=$(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].Status')

    while [ "$refresh_status" != "Successful" ]; do
      refresh_status=$(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].Status')

      if [[ "$refresh_status" == "InProgress" || "$refresh_status" == "Pending" ]]; then
        echo "Instance refresh in progress
        Start time: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].StartTime')
        Status reason: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].StatusReason')
        Percentage complete: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].PercentageComplete')
        "
        sleep $attempt_timeout

      elif [[ "$refresh_status" == "Successful" ]]; then
        echo "Instance refresh complited successfuly
        End time: $(aws autoscaling describe-instance-refreshes --auto-scaling-group-name $as_full_name --instance-refresh-ids $REFRESH_ID | jq -r '.InstanceRefreshes[].EndTime')
        "
        break

      else
        echo "Instance refresh is $refresh_status"
        break

      fi

    done

  fi

fi

echo "########### END AT $(date) ###########"
