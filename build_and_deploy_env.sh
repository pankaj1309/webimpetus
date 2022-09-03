#!/bin/bash

############ This bash script deploys WebImpetus CI4 project (mariadb, php_lamp, phpmyadmin)
############ as docker container into dev,test or prod environment using docker compose files.

set -x

###### Set some variables
HOST_ENDPOINT_UNSECURE_URL="http://localhost:8078"
WORKSPACE_DIR="/tmp/webimpetus/"

##### Set some variables

if [[ -z "$1" ]]; then
   echo "env is empty, so setting target_env to development (default)"
   target_env="development"
else
   echo "env is NOT empty, so setting target_env to $1"
   target_env=$1
fi

target_env_short=$target_env

if [[ "$target_env" == "production" ]]; then
target_env_short="prod"
fi

if [[ "$target_env" == "development" ]]; then
target_env_short="dev"
fi

mkdir -p ${WORKSPACE_DIR}
chmod 777 ${WORKSPACE_DIR}

rm -rf ${WORKSPACE_DIR}*
cp -r ../webimpetus/* ${WORKSPACE_DIR}

mv ${WORKSPACE_DIR}${target_env_short}.env ${WORKSPACE_DIR}.env

cd ${WORKSPACE_DIR}

docker-compose -f docker-compose.yml down
docker-compose -f docker-compose.yml up -d --build
docker-compose -f docker-compose.yml ps

if [[ "$target_env_short" == "dev" ]]; then
chmod +x reset_containers.sh
/bin/bash reset_containers.sh $target_env
fi

sleep 2

curl -IL $HOST_ENDPOINT_UNSECURE_URL
echo "Open Host endpoint..."

os_type=$(uname -s)

if [[ "$os_type" == "Darwin" ]]; then
open $HOST_ENDPOINT_UNSECURE_URL
fi

if [[ "$os_type" == "Linux" ]]; then
xdg-open $HOST_ENDPOINT_UNSECURE_URL
fi













# cp -r ../webimpetus/* /tmp/$workdirname_file
# mv /tmp/$workdirname_file/dev.env /tmp/$workdirname_file/.env
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml down
# # docker-compose build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml up -d --build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml ps
# mv /tmp/$workdirname_file/prepare_workspace_env.sh .
#mv /tmp/prepare_workspace_env.sh .
# sleep 30
# #./reset_env.sh
# sudo -S rm -Rf ci4/
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/data
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/config
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/