FROM debian:jessie

ENV DEBIAN_FRONTEND noninteractive

RUN \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get -y install \
    lighttpd \
    php5-common \
    php5-cgi \
    php5-sqlite \
    php-xml-parser \
    php5 \
    unzip \
    wget

RUN \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/*

RUN \
  cd /root && \
  wget https://github.com/fguillot/miniflux/archive/master.zip -O miniflux.zip && \
  unzip miniflux.zip -d /var/www && \
  mv /var/www/miniflux-master/* /var/www/ && \
  rm miniflux.zip && \
  rm -r /var/www/html && \
  rm -r /var/www/miniflux-master && \
  chmod +x /var/www/data

RUN \
  chown -R www-data:www-data /var/www && \
  rm /etc/lighttpd/lighttpd.conf

ADD \
  scripts/docker/lighttpd.conf /etc/lighttpd/lighttpd.conf

EXPOSE 80

CMD ["/usr/sbin/lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]