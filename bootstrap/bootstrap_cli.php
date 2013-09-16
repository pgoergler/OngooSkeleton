<?php

$app->register(new \Ongoo\Silex\OngooServiceProvider(), array(
    'bundle.include_routes' => false,
));

$app->register(new \Logging\LoggingServiceProvider\LoggingServiceProvider(), array(
    'ongoo.loggers' => $app['configuration']->get('Loggers'),
    'logger.class' => '\Logging\LoggingServiceProvider\Logger',
    'logger.directory' => $app['dir_log'],
));

$app->register(new \Quartz\QuartzServiceProvider\QuartzServiceProvider(), array(
    'quartz.databases' => \Ongoo\Core\Configuration::getInstance()->get('Databases'))
);

return $app;
