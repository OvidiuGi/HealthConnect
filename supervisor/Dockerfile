FROM licenta-php-image

RUN apt-get update && apt-get install -y openssh-server apache2 supervisor
RUN mkdir -p /var/log/supervisor
RUN apt-get install -y cron

RUN crontab /etc/crontab

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]