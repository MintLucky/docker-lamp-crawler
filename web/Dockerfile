from web

COPY ./app /app
COPY ./sql /sql
COPY ./scripts /scripts
RUN echo '104.198.59.174 mariadb' >> /etc/hosts
#CMD ["apache2-foreground"]
CMD ["/scripts/run.sh"]
