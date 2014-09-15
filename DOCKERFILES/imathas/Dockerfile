# IMathAS as a container
# http://imathas.com
#
# Remember to start a mysql server

# Adapted from example from https://registry.hub.docker.com/u/tutum/apache-php/
FROM tutum/apache-php:latest
# Install Git
RUN apt-get -y update
RUN apt-get -y install git

RUN rm -fr /app && git clone https://github.com/mfrellum/IMathAS.git /app
#RUN rm -fr /app && git clone --single-branch https://github.com/mfrellum/IMathAS.git /app
EXPOSE 80

CMD ['/run.sh'] 
