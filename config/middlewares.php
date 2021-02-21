<?php

use Http\Middleware\JwtMiddleware;

return array(
    'auth:api' => JwtMiddleware::class
);