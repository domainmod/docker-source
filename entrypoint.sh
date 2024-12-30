#!/bin/bash
printenv | grep -v "no_proxy" >> /etc/environment

usermod -u ${PUID} domainmod
groupmod -g ${PGID} domainmod
su -s /bin/bash -c 'id' domainmod

if [ -n "$(ls -A /var/www/new_version)" ]; then

    cp -R /var/www/new_version/. /var/www/html \
    && rm -Rf /var/www/new_version

fi

SAMPLE_CONFIG_FILE="/var/www/html/_includes/config.SAMPLE.inc.php"
OUTPUT_CONFIG_FILE="/var/www/html/_includes/config.inc.php"

# Read the sample config file and replace the placeholders with environment variables
sed -e "s|/dm|${DOMAINMOD_WEB_ROOT}|g" \
    -e "s|localhost|${DOMAINMOD_DATABASE_HOST}|g" \
    -e "s|db_name|${DOMAINMOD_DATABASE}|g" \
    -e "s|db_username|${DOMAINMOD_USER}|g" \
    -e "s|dbPassword123|${DOMAINMOD_PASSWORD}|g" \
    $SAMPLE_CONFIG_FILE > $OUTPUT_CONFIG_FILE

chown -R domainmod:domainmod /var/www/html

chmod 777 /var/www/html/temp

service cron start

exec "$@"

