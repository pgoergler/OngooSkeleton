<?php

return array(
    'all' => array(),
    'dev' => array(
        'loggers' => array(
            'root' => array(
                'appenders' => array('webfile'),
            ),
            'stdout' => array(
                'appenders' => array('stdout'),
            ),
            'cli' => array(
                'appenders' => array('cli'),
            ),
        ),
        'appenders' => array(
            'stdout' => array(
                'class' => '\Logging\Appenders\EchoAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%file%][%function%@%line%][%name%]',
            ),
            'webfile' => array(
                'class' => '\Logging\Appenders\FileAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%file%][%function%@%line%][%name%]',
                'param' => array(
                    'filename' => '/tmp/dev_logs.log',
                ),
            ),
            'cli' => array(
                'class' => '\Logging\Appenders\CliAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%file%][%function%@%line%][%name%]',
            ),
        ),
    ),
    'prod' => array(
        'loggers' => array(
            'root' => array(
                'appenders' => array('webfile'),
            ),
            'stdout' => array(
                'appenders' => array('stdout'),
            ),
            'cli' => array(
                'appenders' => array('cli', 'cli-file'),
            ),
        ),
        'appenders' => array(
            'stdout' => array(
                'class' => '\Logging\Appenders\EchoAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%file%][%function%@%line%][%name%]',
            ),
            'webfile' => array(
                'class' => '\Logging\Appenders\FileAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%file%][%function%@%line%][%pid%][%name%]',
                'param' => array(
                    'filename' => '%dir_log%/web_%today%.log',
                ),
            ),
            'cli' => array(
                'class' => '\Logging\Appenders\CliAppender',
                'levels' => 'INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY',
                'prefix' => '[%datetime%][%level%][%file%][%function%@%line%][%name%]',
            ),
            'cli-file' => array(
                'class' => '\Logging\Appenders\FileAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%file%][%function%@%line%][%name%]',
                'param' => array(
                    'filename' => '%dir_log%/cli_%app%_%today%.log',
                ),
            ),
        ),
    ),
    'test' => array(
        'loggers' => array(
            'root' => array(
                'appenders' => array('webfile'),
            ),
            'stdout' => array(
                'appenders' => array('stdout'),
            ),
            'cli' => array(
                'appenders' => array(/*'cli',*/ 'cli-file'),
            ),
        ),
        'appenders' => array(
            'stdout' => array(
                'class' => '\Logging\Appenders\EchoAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%test_method%][%file%][%function%@%line%][%name%]',
            ),
            'webfile' => array(
                'class' => '\Logging\Appenders\FileAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%test_method%][%file%][%function%@%line%][%name%]',
                'param' => array(
                    'filename' => '/tmp/tests_web.log',
                ),
            ),
            'cli' => array(
                'class' => '\Logging\Appenders\CliAppender',
                'levels' => 'INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY',
                'prefix' => '[%datetime%][%level%][%file%][%function%@%line%][%name%]',
            ),
            'cli-file' => array(
                'class' => '\Logging\Appenders\FileAppender',
                'levels' => 'ALL',
                'prefix' => '[%datetime%][%level%][%pid%][%test_method%][%file%][%function%@%line%][%name%]',
                'param' => array(
                    'filename' => '/tmp/tests.log',
                ),
            ),
        ),
    ),
);
