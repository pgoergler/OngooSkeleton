<?php

$app->register(new \Ongoo\Silex\OngooServiceProvider(), array(
    'bundle.include_routes' => true,
));

$app->register(new Silex\Provider\TwigServiceProvider(), array());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new \Ongoo\Twig\TwigOngooExtensionProvider(), array());


/*
// ENABLE TRANSLATION
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => 'en_GB',
    'locale' => 'fr_FR'
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
    $translator->addResource('yaml', __LOCALES_DIR .'/fr.yml', 'fr_FR');
    return $translator;
}));
$app->before(function(\Symfony\Component\HttpFoundation\Request $request) use(&$app)
            {
                $app['_route'] = $request->get('_route');

                if (isset($_COOKIE['_locale']))
                {
                    $app['session']->set('_locale', $_COOKIE['_locale']);
                }
                $app['translator']->setLocale($app['session']->get('_locale'));
            });
 */

$app->register(new \Logging\LoggingServiceProvider\LoggingServiceProvider(), array(
    'ongoo.loggers' => $app['configuration']->get('Loggers'),
    'logger.class' => '\Logging\LoggingServiceProvider\Logger',
    'logger.directory' => $app['dir_log'],
));

$app->register(new \Quartz\QuartzServiceProvider\QuartzServiceProvider(), array(
    'quartz.databases' => \Ongoo\Core\Configuration::getInstance()->get('Databases'),
        )
);
$app->finish(function() use(&$app)
        {
            $app['orm']->closeAll();
        });

$app->register(new \Quartz\QuartzGuard\QuartzGuardServiceProvider(), array(
    'session.storage.save_path' => \Ongoo\Core\Configuration::getInstance()->get('Ongoo.session_path', '/tmp/'),
    'session.storage.options' => array(
        'cookie_lifetime' => 86400,
    ),
));

$app['session.activate'] = $app->protect(function() use (&$app)
{
    $app['session']->start();
});


return $app;