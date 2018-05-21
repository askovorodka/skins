FROM maryville/rabbitmq
MAINTAINER Alan Bondarchuk <imacoda@gmail.com>

RUN apk add --update tzdata && \

    # Set Europe/Moscow as local timezone
    cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime && \
    echo "Europe/Moscow" > /etc/timezone && \

    # Remove other timezones
    apk del tzdata

EXPOSE      5671/tcp 5672/tcp 15672/tcp 15671/tcp
VOLUME      /var/lib/rabbitmq