#!/bin/sh

while ! nc -w 2 -z mtwplatform-db 3306; do   
  sleep 1
done

robo site:update
