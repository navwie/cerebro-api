#!/bin/sh

wait_time=540
interval_time=2
host=$1
healthcheck_url="${host}"
expected_image_tag=$2

command -v curl || sudo apt-get update && sudo apt-get install curl -y

if [ -z "${url}" ] && [ -z "${expected_image_tag}" ]; then
  echo "Usage:"
  echo "   ${0} <file_name> <expected_image_tag>"
  exit 1
fi

now_time=$(date +%s)
end_time=$((${now_time} + ${wait_time}))

echo "Ascertaining if ${healthcheck_url} is deployed with tag ${expected_image_tag} within ${wait_time}s"

while [ "${end_time}" -gt $(date +%s) ]; do
  deployed_image_tag=$(curl -s -k -I -X GET https://${healthcheck_url} | grep -Fi 'Github_sha' | awk '{print $2}' | tr -d '\r')

  if [ ! -z "${deployed_image_tag}" ] && [ "${deployed_image_tag}" = "${expected_image_tag}" ]; then
    echo "Successfully deployed ${expected_image_tag}"
    exit 0
  else
    echo "Currently deployed with ${deployed_image_tag}, checking again in ${interval_time}s"
  fi

  sleep ${interval_time}
done

echo "Deployment did not occur in ${wait_time}s" 1>&2
exit 1
