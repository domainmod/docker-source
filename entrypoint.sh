#!/bin/bash
printenv | grep -v "no_proxy" >> /etc/environment

usermod -u ${PUID} domainmod
groupmod -g ${PGID} domainmod
su -s /bin/bash -c 'id' domainmod

if [ -n "$(ls -A /var/www/new_version)" ]; then

    cp -R /var/www/new_version/. /var/www/html \
    && rm -Rf /var/www/new_version

fi

chown -R domainmod:domainmod /var/www/html

chmod 777 /var/www/html/temp

service cron start

exec "$@"

