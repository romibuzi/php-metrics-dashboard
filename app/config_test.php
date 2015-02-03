<?php

$rootDir = dirname(dirname(__FILE__));

$config = [
    'version' => '0.1',

    'root_dir' => $rootDir,
    'debug' => true,
    'monolog.name' => 'php-metrics-dashboard',
    'monolog.level' => \Monolog\Logger::DEBUG,
    'monolog.logfile' => __DIR__ . '/logs/app_test.log',

    'phpmetrics.phar_path' => $rootDir . '/bin/phpmetrics.phar',

    'projects.config_file' => $rootDir . '/projects_test.json',

    'projects.report_folder' => $rootDir . '/web/reports/test/',
    'projects.source_folder' => $rootDir . '/var/projects/test/',
];

return $config;
