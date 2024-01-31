#!/bin/bash
current_date="$(date +"%d-%m-%Y_%H-%M")"

help="$(basename "$0") [-h] [-u user] [-p password] [-s server_address] [-a backup/restore] -- backup and restore Cerebro API database script

Script creates the database dump in the source directory folder.

where:
    -h show this help text
    -u set the database username
    -p set the database password
    -s server(host) address
    -a action [restore / backup] database"

while getopts :hu:p:a:s:t:d: option; do
  case "$option" in
    h) echo "$help"
       exit 1
       ;;
    u) user=$OPTARG ;;
    p) password=$OPTARG ;;
    s) server=$OPTARG ;;
    a) action=$OPTARG ;;
    t) transfer=$OPTARG ;;
    d) directory=$OPTARG ;;
    :) printf "missing argument for -%s\n" "$OPTARG" >&2
       echo "$help" >&2
       exit 1
       ;;
   \?) printf "illegal option: -%s\n" "$OPTARG" >&2
       echo "$help" >&2
       exit 1
       ;;
  esac
done

shift $((OPTIND - 1))

if [[ -z "$action" ]]; then
    echo 'Action required'
    echo "$help" >&2
    exit 1
fi

if [[ -z "$user" ]]; then
    echo 'User required'
    echo "$help" >&2
    exit 1
fi

if [[ -z "$password" ]]; then
    echo 'Password required'
    echo "$help" >&2
    exit 1
fi

if [[ "$action" != 'backup' ]] && [[ $action != 'restore' ]]; then
    echo 'Action should be backup or restore'
    echo "$help" >&2
    exit 1
fi

if [[ "$action" == 'backup' ]]; then

  if [[ -n "$directory" ]]; then
      mkdir "$directory" -p
      cd "$directory" || exit 0
  else
      echo "directory: ./backup"
      mkdir ./backup -p
      cd ./backup || exit 0
  fi

  echo "===== $(date +"%H-%M-%S"): DUMP databases ====="

  mysqldump --opt -u $user -p$password -h $server cerebro > ./cerebro-$current_date.sql
  mysqldump --opt -u $user -p$password -h $server cerebro_audit > ./cerebro_audit-$current_date.sql

  zip "cerebro-$current_date.zip" "cerebro-$current_date.sql"
  zip "cerebro_audit-$current_date.zip" "cerebro_audit-$current_date.sql"

  rm cerebro_audit-$current_date.sql
  rm cerebro-$current_date.sql


  if [[ "$transfer" == 'y' ]] || [[ "$transfer" == 'Y' ]]; then
    echo "===== $(date +"%H-%M-%S"): Transferring to the remote server ====="
    scp -i /home/admin/.ssh/dnm-api1.key  "cerebro-$current_date.zip" "ubuntu@cerebro.smissltd.com:/home/ubuntu/docker/cerebro/backup/cerebro-$current_date.zip"
    scp -i /home/admin/.ssh/dnm-api1.key  "cerebro_audit-$current_date.zip" "ubuntu@cerebro.smissltd.com:/home/ubuntu/docker/cerebro/backup/cerebro_audit-$current_date.zip"
  fi
fi

if [[ "$action" == 'restore' ]]; then

  if [[ -z "$directory" ]]; then
      cd "$directory" || exit 0
  else
      cd ./backup || exit 0
  fi

  unzip cerebro-*
  unzip cerebro_audit-*

  cerebroDump=basename"$(find . -name 'cerebro-*.sql')"
  cerebro_auditDump="$(find . -name 'cerebro_audit-*.sql')"

  echo "===== $(date +"%H-%M-%S"): Drop databases ====="

  docker exec cerebro-mysql bash -c "mysqladmin -u $user -p$password drop cerebro -f"
  docker exec cerebro-mysql bash -c "mysqladmin -u $user -p$password drop cerebro_audit -f"

  echo "===== $(date +"%H-%M-%S"): Create an empty databases ====="

  docker exec cerebro-mysql bash -c "mysqladmin -u $user -p$password create cerebro -f"
  docker exec cerebro-mysql bash -c "mysqladmin -u $user -p$password create cerebro_audit -f"

  echo "===== $(date +"%H-%M-%S"): Restoring dump into databases ====="

  docker exec cerebro-mysql bash -c "mysql -u $user -p$password -D cerebro < /dump/${cerebroDump##*/}"
  docker exec cerebro-mysql bash -c "mysql -u $user -p$password -D cerebro_audit < /dump/${cerebro_auditDump##*/}"

  echo "===== $(date +"%H-%M-%S"): Remove sensitive data ====="

  docker exec cerebro-mysql bash -c "/dump/update_database.sh $user $password"

  echo "===== $(date +"%H-%M-%S"): Finished ====="
fi