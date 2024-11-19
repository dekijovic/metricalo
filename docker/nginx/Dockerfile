FROM nginx:1.27

RUN rm /etc/nginx/conf.d/default.conf
ADD site.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
EXPOSE 443
