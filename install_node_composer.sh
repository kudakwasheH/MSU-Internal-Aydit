#!/bin/bash
echo 'khuda..1' | sudo -S curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -S bash -
echo 'khuda..1' | sudo -S DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
echo 'khuda..1' | sudo -S mv composer.phar /usr/local/bin/composer
