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
3. In some cases you may need to set `chmod -R 777 storage/logs`


## Using API documentation
1. 'Scribe' package have been used for creating API documentation.
2. If needed, you can regenerate it by running command `sail artisan scribe:generate`
3. Documentation is at the URL http://localhost:8008/docs
4. First, you need to authorize user by picking 'Login user and create token' point.
5. Then, click 'Try it out' button
6. In the fields below enter email and password:
   1. You can use `start-user@mail.org` or email from DB table 'users';
   2. The Password of any user is '**password**' for simple testing.
   3. Remember, that the access token is valid for **60 minutes**. After that, you need to authenticate anew.
   4. If you feel that you have too many access tokens, you can prune the table by running the command `sail artisan sanctum:prune-expired --hours=24` that removes all old (expired) tokens.
7. When logged in, you'll see the Bearer token in the right panel. Use this one for authorizing any other endpoints.
8. For example, you want to see some 'task'. Click 'Display the specified resource' and fill fields:
   1. <u>Authorization</u> in format 'Bearer ' + token taken from previous point. 
   2. Don't touch other headers
   3. Enter <u>ID</u> you want.
9. Then hit the green button 'Send request'. See the result in the right panel.
