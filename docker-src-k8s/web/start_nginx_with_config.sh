#!/bin/sh

envsubst '$S3FS_SCHEMA $MINIO_SVC_NAME $MINIO_SVC_PORT $S3FS_BUCKETNAME' < /etc/nginx/conf.d/drupal.conf.template > /etc/nginx/conf.d/drupal.conf
nginx -g 'daemon off;'
