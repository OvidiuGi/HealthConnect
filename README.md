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
docker-compose up -d
docker-compose exec healthconnect-php-container composer install
docker-compose exec healthconnect-php-container php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Usage
The application is available at 160.100.0.5

## Authors
- Gireada Ovidiu-Gabriel