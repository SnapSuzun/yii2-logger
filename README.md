Logger for Yii2
===========================

An extension for log messages to [logstash](https://www.elastic.co/products/logstash), file and etc.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist snapsuzun/yii2-logger
```

or add

```
"snapsuzun/yii2-logger": "dev-master"
```

to the require section of your `composer.json` file.

Configuration
-----------

To use this extension for sending logs to `logstash`, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'logger' => [
            'class' => \snapsuzun\yii2logger\logstash\Logger::class,
            'host' => 'localhost',
            'port' => 12201,
            'logIndex' => 'test', // Index of created logs in ElasticSearch
            'user' => 'username', // Username and password for authenticate on logstash server if it need authentication for create log
            'password' => 'password',
            'transportType' => \snapsuzun\yii2logger\logstash\LogstashInterface::TRANSPORT_HTTP // Maybe TRANSPORT_HTTP or TRANSPORT_SOCKET
        ],
    ],
];
```

If you want to send logs to `logstash` asynchronously you can add component of class `snapsuzun\yii2logger\logstash\LoggerAsync`:

```php
return [
    //....
    'components' => [
        'logger' => [
            'class' => \snapsuzun\yii2logger\logstash\async\LoggerAsync::class,
            'host' => 'localhost',
            'port' => 12201,
            'logIndex' => 'test', // Index of created logs in ElasticSearch
            'user' => 'username', // Username and password for authenticate on logstash server if it need authentication for create log
            'password' => 'password',
            'transportType' => \snapsuzun\yii2logger\logstash\LogstashInterface::TRANSPORT_HTTP, // Maybe TRANSPORT_HTTP or TRANSPORT_SOCKET
            'queue' => 'queue',
        ],
    ],
];
```

Asynchronous logger use [yii2-queue](https://github.com/yiisoft/yii2-queue) for sending logs to logstash. You should set `queue` in configuration that may be link to a component of class `\yii\queue\Queue` or array with configuration to create object with some class.
When asynchronous logger create log it create a job instance og `\snapsuzun\yii2logger\logstash\LoggerAsyncSendJob` and put its to queue.

Also, you can add target to default Yii2 log:
```php
return [
    //....
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \snapsuzun\yii2logger\logstash\LogstashTarget::class,
                    'levels' => ['error', 'warning'],
                    'host' => 'localhost',
                    'port' => 12201,
                    'logIndex' => 'test', // Index of created logs in ElasticSearch
                    'user' => 'username', // Username and password for authenticate on logstash server if it need authentication for create log
                    'password' => 'password',
                    'transportType' => \snapsuzun\yii2logger\logstash\LogstashInterface::TRANSPORT_HTTP // Maybe TRANSPORT_HTTP or TRANSPORT_SOCKET
                ]
            ]
        ]
    ]
]
```

or async target:

```php
return [
    //....
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \snapsuzun\yii2logger\logstash\async\LogstashAsyncTarget::class,
                    'levels' => ['error', 'warning'],
                    'host' => 'localhost',
                    'port' => 12201,
                    'logIndex' => 'test', // Index of created logs in ElasticSearch
                    'user' => 'username', // Username and password for authenticate on logstash server if it need authentication for create log
                    'password' => 'password',
                    'transportType' => \snapsuzun\yii2logger\logstash\LogstashInterface::TRANSPORT_HTTP, // Maybe TRANSPORT_HTTP or TRANSPORT_SOCKET
                    'queue' => 'queue'
                ]
            ]
        ]
    ]
]
```

For pushing logs to file add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'logger' => [
            'class' => \snapsuzun\yii2logger\file\Logger::class
        ],
    ],
];
```