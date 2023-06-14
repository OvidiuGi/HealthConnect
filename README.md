# HealthConnect Bachelor's Thesis Project

## Project Description
This project is a part of the Bachelor's Thesis at the University of Tartu.
The project is a web application that allows users to search for doctors and book appointments with them. 
The application is built using the Symfony framework, along with MySQL, Docker, Redis, RabbitMQ.

### Prerequisites

- Docker
- Docker Compose

## Installation
Clone this repository and run the following commands:
```
docker compose build healthconnect-php-service
docker compose up -d
docker exec healthconnect-php-container composer install
docker exec healthconnect-php-container npm install
docker exec healthconnect-php-container npm run watch
docker exec healthconnect-php-container php bin/console doctrine:migrations:migrate
docker exec healthconnect-php-container php bin/console doctrine:fixtures:load --append
```
After the following commands, in .env, change the first line to prod.

## Usage
The application is available at 160.100.0.5
# The admin account is :
```
email: gireada_ovidiu@test.com
password: Parola123
```
# The doctor account is :
```
email: isabel_opris@test.com
passwords: Parola123
```
## Authors
- Gireada Ovidiu-Gabriel