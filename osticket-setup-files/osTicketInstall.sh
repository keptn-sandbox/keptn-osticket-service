#!/usr/bin/env bash

# This script installs OSTicket.
# This installation is IN NO WAY SECURE.
# This script is PURELY for testing purposes and is NOT suitable for PRODUCTION use

sudo apt update -y
sudo apt install unzip apache2 php7.2 php7.2-mysql mysql-server -y
sudo a2enmod rewrite
sudo rm /var/www/html/index.html
mkdir osticket && cd osticket
wget https://github.com/osTicket/osTicket/releases/download/v1.14.1/osTicket-v1.14.1.zip
unzip osTicket-v1.14.1.zip
sudo mv upload/* /var/www/html
sudo mv /var/www/html/include/ost-sampleconfig.php /var/www/html/include/ost-config.php
sudo chmod 0666 /var/www/html/include/ost-config.php
sudo mysql -u root -e "CREATE DATABASE osticket;"
sudo mysql -u root -e "CREATE USER 'osticket'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON * . * TO 'osticket'@'localhost';"
sudo chown ubuntu:ubuntu /etc/apache2/sites-available/000-default.conf
cat <<EOF >> /etc/apache2/sites-available/000-default.conf
<Directory /var/www/>
AllowOverride All
</Directory>
EOF
sudo service apache2 restart

echo ""
echo ""
echo "######################################################################################"
echo "Go to http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)/setup"
echo "To continue the osticket setup."
echo ""
echo "Database = osticket"
echo "Database User / Password = osticket / password"
echo ""
echo "After install you may get an HTTP 500. That's OK. Just refresh the page."
echo ""
echo "When you have completed the browser-based setup, you'll see security warnings. Please run the cleanup.sh script."
echo "######################################################################################"
