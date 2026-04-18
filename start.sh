#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
NGINX_PREFIX="$(dirname $(dirname $(which nginx)))"

mkdir -p /tmp/nginx_client_body /tmp/nginx_proxy /tmp/nginx_fastcgi /tmp/nginx_uwsgi /tmp/nginx_scgi

sed \
  -e "s|SCRIPT_DIR|${SCRIPT_DIR}|g" \
  -e "s|NGINX_MIME_TYPES|${NGINX_PREFIX}/conf/mime.types|g" \
  -e "s|NGINX_FASTCGI_PARAMS|${NGINX_PREFIX}/conf/fastcgi_params|g" \
  "${SCRIPT_DIR}/nginx.conf.template" > /tmp/nginx.conf

cp "${SCRIPT_DIR}/php-fpm.conf.template" /tmp/php-fpm.conf

php-fpm --fpm-config /tmp/php-fpm.conf &

for i in $(seq 1 20); do
  if [ -S /tmp/php-fpm.sock ]; then
    break
  fi
  sleep 0.5
done

exec nginx -c /tmp/nginx.conf -g "daemon off;"
