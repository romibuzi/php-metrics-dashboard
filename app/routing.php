<?php

/**
 * All routes are defined here
 *
 * @link http://silex.sensiolabs.org/doc/usage.html#routing
 * @link http://silex.sensiolabs.org/doc/providers/service_controller.html
 */
$app->get('/', "default_controller:indexAction")->bind('index');
$app->get('/{project}', "default_controller:listReportsAction")->bind('project_reports_list');
$app->get('/{project}/{report}', "default_controller:displayReportsAction")->bind('project_report');
