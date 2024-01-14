# HurryApp API Service



## Deployment

- Clone the repository

```
$ git clone https://github.com/HurryAppHackathon/streaming-api
```

- Copy `.env.example`

```
$ cp .env.example .env
```

- Install `sail`

```
$ docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

- Run services

```
$ ./vendor/bin/sail up -d
```
