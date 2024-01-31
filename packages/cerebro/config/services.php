<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'maxmind' => [
        'link' => env('MAXMIND_LINK', 'https://minfraud.maxmind.com/minfraud/v2.0/factors'),
        'license_key' => env('MAXMIND_LICENSE_KEY', ''),
        'max_value' => env('MAXMIND_MAX_VALUE', 30),
        'user_id' => env('MAXMIND_USER_ID', 0),
        'test' => env('MAXMIND_TEST', true),
        'test_ip' => env('MAXMIND_TEST_IP', '77.122.79.247'),//0.01 risk
    ],

    'twillio' => [
        'sid' => env('TWILLIO_SID', ''),
        'auth_token' => env('TWILLIO_AUTH_TOKEN', ''),
    ],

    'ip_quality_score' => [
        'test' => env('IP_QUALITY_SCORE_TEST', false),
        'auth_token' => env('IP_QUALITY_SCORE_AUTH_TOKEN', ''),
        'ip_link' => 'https://www.ipqualityscore.com/api/json/ip/',
        'phone_link' => 'https://www.ipqualityscore.com/api/json/phone/',
        'email_link' => 'https://www.ipqualityscore.com/api/json/email/',
        'max_value_risk' => env('MAXMIND_MAX_VALUE', 33),
    ],

    'locationApi' => [
        'link' => env('LOCATIONAPI_LINK', 'https://secure.shippingapis.com/ShippingApi.dll'),
    ],

    'aba' => [
        'link' => env('ABA_LINK', 'https://www.routingnumbers.info/api/data.json'),
    ]


];
