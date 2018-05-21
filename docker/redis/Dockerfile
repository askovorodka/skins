FROM redis:alpine
MAINTAINER Alan Bondarchuk <imacoda@gmail.com>

RUN apk add --update tzdata && \

    # Set Europe/Moscow as local timezone
    cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime && \
    echo "Europe/Moscow" > /etc/timezone && \

    # Remove other timezones
    apk del tzdata
