Skins4Real

## Installation
* Setup parameters:
```
parameters:
    database_host: mariadb
    database_port: null
    database_name: drupal
    database_user: drupal
    database_password: drupal
```
* In configuration files use `php` as application hostname instead of `127.0.0.1`, and use `redis` for Redis service
* Build and run `docker-compose up -d`
* Log in php container `docker-compose exec php sh`
* Create administrator account `bin/console fos:user:create <USERNAME> <EMAIL> <PASSWORD> --super-admin`
* Navigate to `localhost:7000/admin`

## Start development
 - clone repo git@github.com:Cases4Real/Skins4Real.git
 - docker login on dockercloud
 - copy .env.dist to .env and modify local parameters
 - copy docker-compose.yml.dist to docker-compose.yml
 - modify local parameters in docker-compose.yml (ports)
 - run docker-compose -f ./docker-compose.yml up --build -d
 - install dependencies in local containers with command from root path folder of project 'docker-compose exec php composer install'
 - update schema db in local project with command 'docker-compose exec php bin/console doctrine:schema:update --force'
 - install migrationds in local project with command 'docker-compose exec php bin/console doctrine:migrations:migrate'
 - create admin account with command 'docker-compose exec php bin/console fos:user:create <USERNAME> <EMAIL> <PASSWORD> --super-admin '
 - navigate to localhost:{YOUR_LOCAL_PORT}
  
##deploy on production server
 - goto prod server with ssh
 - move to path /home/skins4real/git
 - run GIT_WORK_TREE=../src git pull origin HEAD
 - move to path /home/skins4real/src
 - run cache:clear command with 'docker-compose exec php bin/console cache:clear'
 - run if install new Bundle command 'docker-compose exec php composer install'
 - run new migrations with command 'docker-compose exec php bin/console doctrine:migrations:migrate'
 
