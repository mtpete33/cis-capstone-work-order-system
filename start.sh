#!/bin/bash
set -e

mkdir -p /tmp/nginx_client_body /tmp/nginx_proxy /tmp/nginx_fastcgi /tmp/nginx_uwsgi /tmp/nginx_scgi

php-fpm --fpm-config /home/runner/workspace/php-fpm.conf &

for i in $(seq 1 20); do
  if [ -S /tmp/php-fpm.sock ]; then
    break
  fi
  sleep 0.5
done

exec nginx -c /home/runner/workspace/nginx.conf -g "daemon off;"
