FROM nginx:stable-alpine

RUN rm /etc/nginx/conf.d/default.conf

COPY start_nginx_with_config.sh /start_nginx_with_config.sh

COPY drupal.conf /etc/nginx/conf.d/drupal.conf.template

COPY webroot /var/www/webroot

RUN mkdir -p /mnt/pv-files && ln -s /mnt/pv-files /var/www/webroot/sites/default/files

WORKDIR /var/www/webroot

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
