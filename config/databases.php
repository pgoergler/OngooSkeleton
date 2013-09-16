<?php

return array(
    'all' => array(),
    'dev' => array(
        'default' => array(
            'quartz.classname' => '\Ongoo\Quartz\Proxy\PgsqlProxyConnection',
            'dsn' => 'pgsql://user@hostname_dev/project',
            'persistant' => true,
            'extra' => array(
                'logger' => 'root',
            ),
        ),
    ),
    'prod' => array(
        'default' => array(
            'quartz.classname' => '\Quartz\Connection\PgsqlConnection',
            'dsn' => 'pgsql://user@hostname_prod/project',
            'persistant' => true,
            'extra' => array(),
        ),
    ),
    'test' => array(
        'default' => array(
            'quartz.classname' => '\Ongoo\Quartz\Proxy\PgsqlProxyConnection',
            'dsn' => 'pgsql://user@hostname_test/project',
            'persistant' => true,
            'extra' => array(
                'logger' => 'root',
            ),
        ),
    ),
);