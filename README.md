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
1. The documentation described by '**openapi.yaml**' file you can find in project's root directory.
2. To see it just open that file by PhpStorm in the 'Preview' mode. 
3. Also, you can use [OpenAPI editor](https://editor.swagger.io/) by Swagger.
4. In this case, you must open a local 'openapi.yaml' file in Swagger Editor. Then you can make requests to API endpoints.
5. First, you need to authorize user by picking 'Login user and create token' point.
6. Then, click 'Try it out' button
7. In the fields below enter email and password:
   1. You can use `start-user@mail.org` or email from DB table 'users';
   2. The password of any user seeded at installation step is '**password**' for simple testing.
   3. Remember, that the access token is valid for **60 minutes**. After that, you need to authenticate anew.
   4. If you feel that after numerous queries you have too many access tokens, you can prune the table by running the command `sail artisan sanctum:prune-expired --hours=24` 
      that removes all old (expired) tokens.
8. When logged in, you'll see the Bearer token in the 'Response body' below. Use this one for authorizing any other endpoints.
9. The authorization of all endpoints should be done like this: 
   1. Copy authorization token from 'login' response body, something like this 6|hNK6Tr3n8DVuBm8eKFcew6iVBSYvRqJUD81LKe5f63fb4e54 - without quotes.
   2. At the top of page hit the green button 'Authorize'.
   3. Paste the copied token in the opened pop-up . That's enough.
10. For example, you want to see some 'task'. Open tab 'Get task details' and hit 'Try it out' button, then fill the field <u>ID</u> with ID you want.
11. Then hit the blue button 'Execute'. See the result in the Response body field below.
12. Filtering and sorting are realized by separate end-points. These queries select data of authenticated user tasks only. 
