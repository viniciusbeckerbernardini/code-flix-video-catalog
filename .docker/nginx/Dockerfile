FROM nginx:1.15.0-alpine

RUN rm /etc/nginx/conf.d/default.conf
COPY ./nginx.conf /etc/nginx/conf.d

RUN mkdir -p /var/www/public \
    && chown -R nginx:nginx /var/www \
    && chmod -R 755 /var/www

