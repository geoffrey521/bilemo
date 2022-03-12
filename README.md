# bilemo
An API REST project

## Versions
* PHP 8.0
* Symfony 6.0.5
* Doctrine 2.11.1
* Postgresql 13

## Requirement
* PHP
* Symfony
* Docker
* Composer
* yarn

## Steps

1. Clone the project repository

````
git clone https://github.com/geoffrey521/bilemo.git
````

2. Download and install Composer dependencies

```
composer install
```

5. Using Database from docker

Make sure docker is running, run:

````
docker-compose up
````

6. Update database

````
symfony console d:m:m 
````

7. Load data fixtures

````
symfony console d:f:l
````
8. Start server

````
symfony serve
````

Local access:

* Documentation available at :
    * Url: "localhost:8000/api/doc"
* Adminer :
    * "localhost:8080"
        * Auth:
            * server: "database"
            * user: "root"
            * password: "root"
            * database: "bilemo_db"
