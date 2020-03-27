#!/usr/bin/env bash

# Run this script AFTER you've completed the in-browser OSTicket setup.

sudo rm -rf /var/www/html/setup
sudo chmod 0644 /var/www/html/include/ost-config.php
