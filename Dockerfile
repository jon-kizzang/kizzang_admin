FROM 190041820615.dkr.ecr.us-east-1.amazonaws.com/kizzangphptemplate:latest

# Copy this repo into place.
ADD . /var/www/kizzang-admin

# Update the default apache site with the config we created.
ADD kizzang.conf /etc/apache2/sites-enabled/000-default.conf
ADD ports.conf /etc/apache2/ports.conf
RUN a2enmod rewrite
RUN apt-get install -y mcrypt
EXPOSE 8080

# By default start up apache in the foreground, override with /bin/bash for interative.
CMD /usr/sbin/apache2ctl -D FOREGROUND
