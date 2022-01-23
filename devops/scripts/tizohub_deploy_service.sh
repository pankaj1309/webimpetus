#!/bin/bash

export KUBECONFIG=/var/www/html/writable/kube_config_auth
aws eks update-kubeconfig --name $KUBENETES_CLUSTER_NAME --region eu-west-2

kubectl apply -f /var/www/html/writable/tizohub_deployments/service-$SERVICE_UUID.yaml

#kubectl get pods
#kubectl get pods -o json