#!/usr/bin/env php
<?php
include __DIR__ . '/../bootstrap/constantes.php';
$app = include __BOOTSTRAP_DIR . '/initialize.php';

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

$application = new \Symfony\Component\Console\Application();

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__TASKS_DIR));
foreach ($files as $file)
{
    if (preg_match("#Task\.php\$#", $file))
    {
        $classes = get_declared_php_classes($file);
        foreach ($classes as $class)
        {
            $clazz = new ReflectionClass($class);
            if ($clazz->IsInstantiable() && $clazz->isSubclassOf('\Ongoo\Core\Task'))
            {
                $obj = new $class($app);
                $application->add($obj);
            }
        }
    }
}

$application->run();