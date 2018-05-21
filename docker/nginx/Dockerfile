FROM nginx:alpine
MAINTAINER Alan Bondarchuk <imacoda@gmail.com>

COPY nginx.conf /etc/nginx/nginx.conf

VOLUME /var/www/html
EXPOSE 80
CMD [ "nginx", "-g", "daemon off;" ]
