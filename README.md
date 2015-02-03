PhpMetrics Dashboard
=====================

Continuously generate PhpMetrics reports (https://github.com/Halleck45/PhpMetrics) for your projects and display them via a web interface.

[![Build Status](https://travis-ci.org/Romibuzi/php-metrics-dashboard.svg?branch=master)](https://travis-ci.org/Romibuzi/php-metrics-dashboard)

Getting started
-----

Requirements :

- Linux or Mac
- PHP 5.4+
- Git executable on your machine

Install depedencies via composer :

    composer install --no-dev

Edit your configuration :

    cp projects.json.dist projects.json

Take a look at `projects.json.dist` to see a full working example.

**Note** : all projects must be git repositories. They are cloned in `var/projects` in order to run PhpMetrics tool on them.
Your server must have a ssh authorized key if you want to fetch private repositories (https://help.github.com/articles/generating-ssh-keys/).


Then launch the command which will generate reports (you can put it as a crontask to generate reports of your projects each day, week, or month) :

    php /path/to/php-metrics-dashboard/bin/console generate-reports

Reports will be put in `web/reports` folder.

Finally, setup your favorite webserver to point to `web/index.php` file, and you can browse reports of your different projects on the web interface.


Thanks
-----

To Jean-François Lépine <http://blog.lepine.pro> for this fabulous tool !