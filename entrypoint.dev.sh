#!/bin/sh
chmod -R 777 ./

cd /var/www/html

composer install
bin/console doctrine:database:create --if-not-exists --no-interaction
bin/console doctrine:migrations:migrate  --no-interaction
bin/console doctrine:fixtures:load --no-interaction
vendor/bin/phpstan analyse src
vendor/bin/php-cs-fixer fix src

bin/console doctrine:database:create --env=test --if-not-exists --no-interaction
bin/console doctrine:migrations:migrate --env=test --no-interaction
bin/console doctrine:fixtures:load --env=test --no-interaction
bin/phpunit

chown -R www-data:www-data ./var/* ./public/*

exec php-fpm