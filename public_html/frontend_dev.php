<?php

include __DIR__ . '/../bootstrap/constantes.php';

$app = include __BOOTSTRAP_DIR . '/initialize.php';
$app['debug'] = true;
$app['application.mode'] = 'dev';
include __BOOTSTRAP_DIR . '/bootstrap_web.php';

$app['bundle.register']('Common');
$app['bundle.register']('ProjectName', true);
$app['bundle.register.menu']('ProjectName');

\Ongoo\Core\Configuration::getInstance()->set('application', $app);
$brand = 'ProjectName';
$app['configuration']->set('App.brand', $brand);
$app->run();