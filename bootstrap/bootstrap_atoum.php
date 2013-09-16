<?php

include __DIR__ . '/constantes.php';

$app = include __BOOTSTRAP_DIR . '/initialize.php';
$app['debug'] = true;
$app['application.mode'] = 'test';
include __BOOTSTRAP_DIR . '/bootstrap_cli.php';

$app->boot();

$root = $app['logger.factory']->get('cli');
$root->set('app', '');
$app['logger.factory']->add($root, 'root');
$app['logger'] = $root;

\Ongoo\Core\Configuration::getInstance()->set('application', $app);
