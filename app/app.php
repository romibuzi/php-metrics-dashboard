<?php

require_once __DIR__ . '/../vendor/autoload.php';

$env = getenv('ENV');

if (! empty($env)) {
    $config = require __DIR__  . '/config_' . $env . '.php';
} else {
    $config = require __DIR__  . '/config.php';
}

$app = new Silex\Application($config);

/**
 * Register Services
 */
$app->register(new Silex\Provider\MonologServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
        'twig.path' => __DIR__ . '/../src/views',
        'twig.options' => [
            'debug' => $config['debug'],
            'cache' => __DIR__ . '/cache/twig',
        ]
]);

$app['file_system'] = new \Symfony\Component\Filesystem\Filesystem();

$app['git_wrapper'] = new \GitWrapper\GitWrapper();

/**
 * Register controllers as services
 * @link http://silex.sensiolabs.org/doc/providers/service_controller.html
 **/
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['default_controller'] = new \PhpMetricsDashboard\Controller\DefaultController(
    $app['projects.report_folder'],
    $app['twig'],
    $app['logger']
);

// Include routing
include __DIR__ . '/routing.php';

return $app;
