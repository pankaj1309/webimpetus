#!/bin/bash

############ This bash script deploys WebImpetus CI4 project (mariadb, php_lamp, phpmyadmin)
############ as docker container into dev,test or prod environment using docker compose files.

set -x

if [[ -z "$1" ]]; then
   echo "env is empty, so setting target_env to development (default)"
   target_env="development"
else
   echo "env is NOT empty, so setting target_env to $1"
   target_env=$1
fi

if [[ -z "$2" ]]; then
   echo "action is empty, so setting action to start (default)"
   env_action="start"
else
   echo "action is NOT empty, so setting action to start (default)"
   env_action=$2
fi

target_env_short=$target_env

if [[ "$target_env" == "production" ]]; then
target_env_short="prod"
fi

if [[ "$target_env" == "development" ]]; then
target_env_short="dev"
fi

###### Set some variables
HOST_ENDPOINT_UNSECURE_URL="http://localhost:8078"

##### Set some variables
WORKSPACE_DIR=$(pwd)

if [[ "$target_env_short" == "test" ]]; then
WORKSPACE_DIR="/tmp/webimpetus"
mkdir -p ${WORKSPACE_DIR}
chmod 777 ${WORKSPACE_DIR}
rm -rf ${WORKSPACE_DIR}*
cp -r ../webimpetus/* ${WORKSPACE_DIR}/
fi

if [[ "$target_env_short" == "prod" ]]; then
WORKSPACE_DIR="/tmp/webimpetus"
mkdir -p ${WORKSPACE_DIR}
chmod 777 ${WORKSPACE_DIR}
rm -rf ${WORKSPACE_DIR}*
cp -r ../webimpetus/* ${WORKSPACE_DIR}/
fi

if [[ "$target_env_short" == "dev" ]]; then
echo "No need to move dev env files"
else
mv ${WORKSPACE_DIR}/${target_env_short}.env ${WORKSPACE_DIR}/.env
fi
cd ${WORKSPACE_DIR}/

if [[ "$env_action" == "stop" ]]; then
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" down
fi

if [[ "$env_action" == "start" ]]; then
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" down
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" up -d --build
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" ps
fi

if [[ "$target_env_short" == "dev" && "$env_action" == "start" ]]; then
chmod +x reset_containers.sh
/bin/bash reset_containers.sh $target_env
fi

if [[ "$env_action" == "start" ]]; then

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