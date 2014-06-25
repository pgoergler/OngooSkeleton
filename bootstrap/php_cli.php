<?php

include __DIR__ . '/constantes.php';

$app = include __BOOTSTRAP_DIR . '/initialize.php';
$app['debug'] = true;
$app['application.mode'] = isset($application_mode) ? $application_mode : 'dev';
include __BOOTSTRAP_DIR . '/bootstrap_cli.php';

$app->boot();

$root = $app['logger.factory']->get('cli');
$root->set('app', '');
$app['logger.factory']->add($root, 'root');
$app['logger'] = $root;

$app['client_id'] = 'cli';

function get_declared_php_classes($file)
{
    if( $file instanceof \Symfony\Component\Finder\SplFileInfo)
    {
        $file = $file->getRealPath();
    }

    if( in_array($file, get_included_files()) )
    {
        return array();
    }

    $declared = get_declared_classes();
    include $file;
    return array_diff(get_declared_classes(), $declared);
}

\Ongoo\Core\Configuration::getInstance()->set('application', $app);
return $app;