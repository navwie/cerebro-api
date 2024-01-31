<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sites management variables
    |--------------------------------------------------------------------------
    |
    |
    */
    'nginx_template' => env('NGINX_TEMPLATE', 'nginx-template.conf'),
    'nginx_ssl_template' => env('NGINX_SSL_TEMPLATE', 'nginx-template-ssl.conf'),
    'nginx_forcessl_template' => env('NGINX_FORCESSL_TEMPLATE', 'nginx-template-forcessl.conf')
];
