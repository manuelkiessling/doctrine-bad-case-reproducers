# Doctrine Zombie Entities: Reproducer

## Setup

    composer install

    docker run -p 127.0.0.1:3306:3306 -e MYSQL_ROOT_PASSWORD=secret -d mariadb:10.6.16 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci

    php bin/console doctrine:database:create
