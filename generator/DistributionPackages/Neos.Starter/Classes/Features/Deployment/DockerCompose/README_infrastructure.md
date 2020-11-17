## Prerequisites

- You need [Docker](https://docker.com) installed. The instructions work on Mac, Linux and Windows.
- You need Composer installed on your host machine (and thus PHP).

## Development Setup

Preparation:

- Run `composer install`
- Run `docker-compose pull`
- Run `docker-compose build --pull`

Start everything:

- Run `docker-compose up -d`

Stopping everything:

- Run `docker-compose down`

When you want to remove all persistent data (Data/Persistent), and the database,
you need to run `docker-compose down -v` to remove all volumes.
