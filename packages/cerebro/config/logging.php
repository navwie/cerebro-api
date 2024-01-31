<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Formatter\LineFormatter;


$awsSettings = [
    'driver' => 'custom',
    'region' => env('CLOUDWATCH_LOG_REGION', 'us-east-1'),
    'credentials' => [
        'key' => env('CLOUDWATCH_LOG_KEY', ''),
        'secret' => env('CLOUDWATCH_LOG_SECRET', '')
    ],
    'version' => env('CLOUDWATCH_LOG_VERSION', 'latest'),
    'formatter' => function ($configs) {
        return new LineFormatter(
            '%level_name%: %message% %context% %extra%',
            //"[%datetime%] %level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true,
            true
        );
    },
    'batch_size' => env('CLOUDWATCH_LOG_BATCH_SIZE', 10000),
    'via' => \Pagevamp\Logger::class,
    'retention' => env('CLOUDWATCH_LOG_RETENTION_DAYS', 14),
    'group_name' => env('CLOUDWATCH_LOG_GROUP_NAME', env('APP_NAME')),
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'get_click_id_request' => array_merge($awsSettings, [
            'name' => 'get_click_id_request',
            'stream_name' => 'Get Click Id Request',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'phone_check' => array_merge($awsSettings, [
            'name' => 'phone_check',
            'stream_name' => 'Phone check',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'ip_quality_score' => array_merge($awsSettings, [
            'name' => 'ip_quality_score',
            'stream_name' => 'Ip quality score',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'validate_request_id' => array_merge($awsSettings, [
            'name' => 'validate_request_id',
            'stream_name' => 'Validate request id',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'import_validation_errors' => array_merge($awsSettings, [
            'name' => 'import_validation_errors',
            'stream_name' => 'Import validation errors',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'debugging' => array_merge($awsSettings, [
            'name' => 'debugging',
            'stream_name' => 'Debugging',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'debugging_timeout' => array_merge($awsSettings, [
            'name' => 'debugging_timeout',
            'stream_name' => 'Debugging timeout',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'debugging_reapply' => array_merge($awsSettings, [
            'name' => 'debugging_reapply',
            'stream_name' => 'Debugging reapply',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),
        'update_error' => array_merge($awsSettings, [
            'name' => 'update_error',
            'stream_name' => 'Update Error',
            'turn' => env('LOG_UPDATE_ERROR', true),
        ]),
        'reapply_request' => array_merge($awsSettings, [
            'name' => 'reapply_request',
            'stream_name' => 'Reapply Request',
            'turn' => env('LOG_REAPPLY_REQUEST', true),
        ]),
        'dnm_send_request_error' => array_merge($awsSettings, [
            'name' => 'dnm_send_request_error',
            'stream_name' => 'Dnm Send Request Error',
            'turn' => env('LOG_SEND_REQUEST_ERROR', true),
        ]),
        'dnmstats' => array_merge($awsSettings, [
            'name' => 'dnmstats',
            'stream_name' => 'Dnm Stats',
            'turn' => env('LOG_DNMSTATS', true),
        ]),
        'decisions' => array_merge($awsSettings, [
            'name' => 'decisions',
            'stream_name' => 'Decisions',
            'turn' => env('LOG_DECISION', true),
        ]),
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => array_merge($awsSettings, [
            'name' => 'single',
            'stream_name' => 'Single',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),

        'daily' => array_merge($awsSettings, [
            'name' => 'daily',
            'stream_name' => 'Daily',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),

        'ssl_management' => array_merge($awsSettings, [
            'name' => 'SSLManagement',
            'stream_name' => 'SSLManagement',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => array_merge($awsSettings, [
            'name' => 'syslog',
            'stream_name' => 'SysLog',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),

        'errorlog' => array_merge($awsSettings, [
            'name' => 'errorlog',
            'stream_name' => 'ErrorLog',
            'level' => env('LOG_LEVEL', 'debug'),
        ]),

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => array_merge($awsSettings, [
            'name' => 'emergency',
            'stream_name' => 'Emergency',
        ]),

        'shell' => array_merge($awsSettings, [
            'name' => 'shell',
            'stream_name' => 'Shell',
        ]),

        'cloudwatch' => array_merge($awsSettings, [
            'name' => 'test-stream',
            'stream_name' => 'Test Api Stream',
        ]),
    ],

];
