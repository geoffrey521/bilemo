[![SymfonyInsight](https://insight.symfony.com/projects/4c94dc9c-ac87-4b43-91ce-60af1ec0ba72/big.svg)](https://insight.symfony.com/projects/4c94dc9c-ac87-4b43-91ce-60af1ec0ba72)

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

Run
````
git clone https://github.com/geoffrey521/bilemo.git
````

2. Download and install Composer dependencies

In the base of the project folder: run
```
composer install
```

3. Using Database

* You can use our database configuration in docker-compose.yml
For more informations about docker, visit https://docs.docker.com/

Make sure docker is running, run:

````
docker-compose up
````

* Or you can use your database by modifying the .env file

DATABASE_URL="postgresql://127.0.0.1:5432/bilemo_db?serverVersion=13&charset=utf8"


4. Update database

````
symfony console d:m:m 
````

5. Load data fixtures

````
symfony console d:f:l
````
6. Start server

````
symfony serve
````

Local access:

* Documentation available at :
    * Url: https://localhost:8000/api/doc
* Adminer :
    * "localhost:8080"
        * Auth:
            * server: "database"
            * user: "root"
            * password: "root"
            * database: "bilemo_db"

    * Default customers access :
    * You can get a token by using this credentials in api login at: https://localhost:8000/api/doc
        * email : 	email@test.test
        * password :  pass
