# TO DO List

##### Simple headless API based on Laravel framework.

## Installation

1. After cloning project you need to run composer packages installation:
    ```
        docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php83-composer:latest \
        composer install --ignore-platform-reqs
    ```
2. As Docker containers this project uses the Laravel Sail package. To run it you need:     
   1. **copy** '.env.example' file to '.env'. This environment file contains set up of container services ports, allowing running without conflicts.
   2. **start** containers:
      1. if you have configured 'sail' command alias in your '.bashrc', you can run `sail up -d`
      2. otherwise run `./vendor/bin/sail up -d`
   3. **run** migrations and seeders accordingly to previous containers start command `./vendor/bin/sail artisan migrate --seed` or `sail artisan migrate --seed`
4. 

## Using API

