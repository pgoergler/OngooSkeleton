<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$dirs = array(
    'root' => __ROOT_DIR,
    'apps' => __APPS_DIR,
    'config' => __CONFIG_DIR,
    'bootstrap' => __BOOTSTRAP_DIR,
    'data' => __DATA_DIR,
    'upload' => __UPLOAD_DIR,
    'lib' => __LIB_DIR,
    'locales' => __LOCALES_DIR,
    'vendor' => __VENDOR_DIR,
    'log' => __LOG_DIR,
    'web' => __WEB_DIR,
    'css' => __CSS_DIR,
    'js' => __JS_DIR,
    'img' => __IMG_DIR,
);

foreach ($dirs as $alias => $const)
{
    $app['dir_' . $alias] = $const;
}

$app['now'] = $app->protect(function(){
    return new \DateTime();
});

$app['silex.classloader'] = $loader;

require __DIR__ . '/../lib/functions.php';

return $app;
