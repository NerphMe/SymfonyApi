# Symfony Api App
Simple Symfony Api Application

`Docker build successfully tested on Linux Ubuntu 18 and Windows 11`.

!! Run `chmod +x entrypoint.dev.sh` until steps !!


1) Open `docker-compose.yml` and check is Database and Nginx ports correct for you. If already used changed it. Same for Nginx + nginx.conf.
2) Run `docker compose up -d`. This command will build containers and automatically will create:
- Database
- Run migrations
- Run composer install
- Run fixtures (dummy data for test.)
- Run php-stan checks
- Run php-cs-fixer
- Run Tests.
- Create and fill test db.
3) In root dir you can check file `swagger.yaml`. Copy content to https://editor.swagger.io. For check.
4) For manually trigger tests. You have to `docker exec -it app sh` then `bin/phpunit`