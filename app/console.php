<?php

use Symfony\Component\Console\Application;

$console = new Application('PhpMetrics Dashboard Application', $app['version']);
$console->setDispatcher($app['dispatcher']);

$reportsCommand = new \Rmb\Command\GenerateReportsCommand($app['file_system'], $app['git_wrapper'], $app['logger']);
$reportsCommand
    ->setPhpMetricsExecutablePath($app['phpmetrics.phar_path'])
    ->setProjectsConfigFile($app['projects.config_file'])
    ->setProjectsReportsFolder($app['projects.report_folder'])
    ->setProjectsSourceFolder($app['projects.source_folder'])
;

$console->add($reportsCommand);

return $console;
