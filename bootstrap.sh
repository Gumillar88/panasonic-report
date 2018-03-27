#!/usr/bin/env bash

# Define MySQL root password
MYSQL_DATABASE='panasonic_report'
MYSQL_PASSWORD='1234'

if [[ -e /var/lock/vagrant-provision ]]; then
    cat 1>&2 << EOD
################################################################################
# To re-run full provisioning, delete /var/lock/vagrant-provision and run
#
#    $ vagrant provision
#
# From the host machine
################################################################################
EOD
    exit
fi

# Update & Upgrade
apt-get update -q
apt-get -y upgrade
apt-get -y dist-upgrade
apt-get -y autoremove

# Install basic tools
apt-get install -y unzip vim git-core curl wget build-essential python-software-properties

# install apache 2.5 and php 7.0
LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
apt-get update

apt-get install apache2 -y --force-yes
apt-get purge php5-common -y
apt-get install php7.0 -y

apt-get --purge autoremove -y

# install mysql and give password to installer
debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"
apt-get install mysql-server -y

# Install php library
apt-get install php7.0-mysql libapache2-mod-auth-mysql libapache2-mod-php7.0 php7.0-mcrypt php7.0-cli php7.0-xmlrpc php7.0-gd php7.0-mbstring php7.0-bcmath -y

# Activate mod_rewrite
sudo a2enmod php7.0
a2enmod rewrite
a2enmod headers

# Allow .htaccess and enable public folder
sed -i "164s/.*/\<Directory\ \/var\/www\/html\/public\>/g" /etc/apache2/apache2.conf
sed -i "166s/.*/AllowOverride\ All/g" /etc/apache2/apache2.conf

# Set character encoding
locale-gen UTF-8

# Edit document root
sed -i "12s/.*/DocumentRoot\ \/var\/www\/html\/public/g" /etc/apache2/sites-available/000-default.conf

service apache2 restart

# Add index.php to DirectoryIndex
echo "
<IfModule mod_dir.c>
        DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
" | tee /etc/apache2/mods-available/dir.conf

# Create .env for Laravel
echo "
APP_ENV=local
APP_DEBUG=true
APP_KEY=ApZuw1ExJKCIG1INxgN0Ne49kHD80QbE
APP_HOME_URL=http://192.168.1.105

DB_HOST=localhost
DB_DATABASE=$MYSQL_DATABASE
DB_USERNAME=root
DB_PASSWORD=$MYSQL_PASSWORD

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
" | tee /var/www/html/.env

# Create database for laravel application
echo "
CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE
  COLLATE utf8_general_ci;
" | mysql --user="root" --password="$MYSQL_PASSWORD"


# Install composer package
cd /var/www/html
# php composer.phar install

# Do laravel migration
php artisan migrate

# Remove default index.html on /var/www/html
rm /var/www/html/index.html

touch /var/lock/vagrant-provision
exit 0
