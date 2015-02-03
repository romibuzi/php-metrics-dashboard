<?php

$rootDir = dirname(dirname(__FILE__));

$config = [
    'version' => '0.1',

    'root_dir' => $rootDir,
    'debug' => true,
    'monolog.name' => 'php-metrics-dashboard',
    'monolog.level' => \Monolog\Logger::ERROR,
    'monolog.logfile' => __DIR__ . '/logs/app.log',

    'phpmetrics.phar_path' => $rootDir . '/bin/phpmetrics.phar',

    'projects.config_file' => $rootDir . '/projects.json',
    'projects.report_folder' => $rootDir . '/web/reports/',
    'projects.source_folder' => $rootDir . '/var/projects/',
];

return $config;
