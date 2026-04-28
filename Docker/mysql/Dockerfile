FROM mysql:8.0
ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=sre_hub

COPY init.sql /docker-entrypoint-initdb.d/
