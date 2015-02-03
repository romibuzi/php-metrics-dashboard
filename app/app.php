<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (! empty($env = getenv('ENV'))) {
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
        'twig.path' => __DIR__ . '/../src/Rmb/views',
        'twig.options' => [
            'cache' => __DIR__ . '/cache/twig',
        ]
]);
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app['file_system'] = $app->share(
    function () use ($app) {
        return new \Symfony\Component\Filesystem\Filesystem();
    }
);
$app['git_wrapper'] = $app->share(
    function () use ($app) {
        return new \GitWrapper\GitWrapper();
    }
);

/**
 * Register controllers as services
 * @link http://silex.sensiolabs.org/doc/providers/service_controller.html
 **/
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['default_controller'] = $app->share(
    function () use ($app) {
        return new \Rmb\Controller\DefaultController($app['projects.report_folder'], $app['twig'], $app['logger']);
    }
);

// Include routing
include __DIR__ . '/routing.php';

return $app;
