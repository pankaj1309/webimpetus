#!/bin/bash

set -x

#aws eks

export KUBECONFIG=/home/bwalia/.kube/k3s-test.yml
#aws eks update-kubeconfig --name $KUBENETES_CLUSTER_NAME --region $AWS_DEFAULT_REGION

kubectl get all -A

#kubectl delete -f /var/www/html/writable/tizohub_deployments/service-$SERVICE_ID.yaml

