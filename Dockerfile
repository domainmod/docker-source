FROM php:8.2.9-apache
ARG COMMIT_ID
ARG BUILD_DATE
ARG SOFTWARE_VERSION
ENV CUSER=domainmod
ENV LOCALE1="en_CA.UTF-8"
ENV LOCALE2="en_US.UTF-8"
ENV LOCALE3="de_DE.UTF-8"
ENV LOCALE4="es_ES.UTF-8"
ENV LOCALE5="fr_FR.UTF-8"
ENV LOCALE6="it_IT.UTF-8"
ENV LOCALE7="nl_NL.UTF-8"
ENV LOCALE8="pl_PL.UTF-8"
ENV LOCALE9="pt_PT.UTF-8"
ENV LOCALE10="ru_RU.UTF-8"
ENV PGID=1000
ENV PUID=1000
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && groupadd --gid "${PGID}" -r $CUSER && useradd -u "${PUID}" -r -g $CUSER -d /usr/src/$CUSER -s /sbin/nologin -c "Docker Image User" $CUSER \
    && apt-get update \
    && apt-get install -y \ 
       cron \
       curl \
       gettext \
       libxml2 \
       libxml2-dev \
       locales \
       tzdata \
    && docker-php-ext-install \
       gettext \
       mysqli \
       pdo \
       pdo_mysql \
       simplexml \
    && apt-get clean -y \
    && rm -Rf /var/lib/apt/lists/*
RUN sed -i -e "s/# $LOCALE1/$LOCALE1/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE2/$LOCALE2/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE3/$LOCALE3/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE4/$LOCALE4/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE5/$LOCALE5/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE6/$LOCALE6/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE7/$LOCALE7/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE8/$LOCALE8/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE9/$LOCALE9/" /etc/locale.gen \
    && sed -i -e "s/# $LOCALE10/$LOCALE10/" /etc/locale.gen \ 
    && locale-gen \
    && echo "LANG=$LOCALE1" > /etc/default/locale
ENV LANG $LOCALE1
ENV LC_ALL $LOCALE1
COPY cron /etc/cron.d/cron
COPY entrypoint.sh /usr/local/bin
COPY php.ini /usr/local/etc/php/php.ini
COPY source/ /var/www/new_version/
RUN chmod 0644 /etc/cron.d/cron \
    && crontab /etc/cron.d/cron \
    && mkdir -p /var/log/cron \
    && chmod +x /usr/local/bin/entrypoint.sh
LABEL org.opencontainers.image.authors='greg@domainmod.org, greg@chetcuti.com' \
      org.opencontainers.image.created="$BUILD_DATE" \
      org.opencontainers.image.description='DomainMOD is a self-hosted open source application used to manage your domains and other Internet assets in a central location' \
      org.opencontainers.image.documentation='https://domainmod.org/docker/' \
      org.opencontainers.image.licenses='GPLv3' \
      org.opencontainers.image.revision="$COMMIT_ID" \
      org.opencontainers.image.source='https://domainmod.org/docker-source/' \
      org.opencontainers.image.title='DomainMOD' \
      org.opencontainers.image.url='https://domainmod.org/docker/' \
      org.opencontainers.image.vendor='DomainMOD, Greg Chetcuti' \
      org.opencontainers.image.version="$SOFTWARE_VERSION"
EXPOSE 80
ENTRYPOINT ["bash", "entrypoint.sh"]
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]

