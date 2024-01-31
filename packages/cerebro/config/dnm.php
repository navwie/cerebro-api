<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DNM API Paths
    |--------------------------------------------------------------------------
    |
    |
    */

    'urls' => [
        'personal' => 'https://dnmstats.com/personal/lead/import/',
        'lead' => 'https://dnmstats.com/lead/import/',
        'get_click_id' => 'https://offers.dnmstats.net/click',
        'focusmarketing' => 'https://focusmarketing.waypointsoftware.io/capture.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | DNM SSN Middleware
    |--------------------------------------------------------------------------
    |
    |
    */
    'ssnMiddleware' => [
        'prefixKey' => 'ssn_middleware:',
        'maxAttempts' => 3,
        'decaySeconds' => 15 * 60,
    ],



    /*
    |--------------------------------------------------------------------------
    | Default DNM Source settings
    |--------------------------------------------------------------------------
    |
    |
    */
    'defaultFormSettings' => [
        'password' => env('USER_PASSWORD', 'jp9mDKAiOMOcUJXC'),
        'postBackAmount' => env('USER_TEST_POST_BACK_AMOUNT', 900),
        'personalMinReq' => env('USER_TEST_PERSONAL_MIN_REQ', 1500),
    ],

    /*
    |--------------------------------------------------------------------------
    | DNM credentials
    |--------------------------------------------------------------------------
    |
    |
    */
    'credentials' => [
        'personalChannelId' => env('USER_TEST_PERSONAL_CHANNEL_ID', 'BflXk'),
        'personalPassword' => env('USER_TEST_PERSONAL_PASSWORD', 'w3Gu6KhDzHgNGmLV3uXpl0D0DHQyIySE'),
        'leadChannelId' => env('USER_TEST_LEAD_CHANNEL_ID', 'aPd0L'),
        'leadPassword' => env('USER_TEST_LEAD_PASSWORD', 'HKI5Ul9wYRcupaIpgXV1DkI44NQPPXMI'),
        'soldFMApiKey' => env('FM_SOLD_API_KEY', '0e69f3ee250ab8d5c1849ecd123e074e'),
        'unSoldFMApiKey' => env('FM_UNSOLD_API_KEY', '75dff2233ef137887c6dbb9ab035380b'),
        'creditCardsFMApiKEy' => env('FM_CREDIT_CARDS_KEY', 'cc4737f0677365bce6290fb33315a04a')
    ],

    /*
    |--------------------------------------------------------------------------
    | DNM turn on/off send some requests
    |--------------------------------------------------------------------------
    |
    |
    */
    'send_post_back_request' => env('DNM_SEND_POST_BACK_REQUEST',false),
    'send_get_click_id_request' => env('DNM_SEND_GET_CLICK_ID_REQUEST',false),
    'sendFM' => env('FM_SEND',false),

    /*
   |--------------------------------------------------------------------------
   | DNM turn on/off test mode
   |--------------------------------------------------------------------------
   |
   |
   */
    'test_mode' => env('DNM_TEST_MODE',false),
    /*
   |--------------------------------------------------------------------------
   | DNM truncateData period in days
   |--------------------------------------------------------------------------
   |
   |
   */
    'truncateDays' => env('DNM_TRUNCATE_DAYS',14),
    /*
  |--------------------------------------------------------------------------
  | DNM CloudFront Distribution Settings
  |--------------------------------------------------------------------------
  |
  |
  */
    'cloudFrontDistributionDescription' => env('CLOUD_FRONT_DISTRIBUTION_DESCRIPTION', null),
    'cloudFrontDistributionInvalidate' => env('CLOUD_FRONT_CREATE_INVALIDATE', true),
];
