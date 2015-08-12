<?php

/**
 * @TODO: Let modules provide default config.
 *
 * Example config.php
 *
 *  return call_user_function(function() {
 *    putenv('SITE_GA_CODE=UA-12344567-8');
 *    $config = require __DIR__ . '/config.default.php';
 *
 *    // Development
 *    // ---------------------
 *    $logger = new Monolog\Logger('vendor-name.project-name');
 *    $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(), Monolog\Logger::DEBUG);
 *    $config['debug'] = true;
 *    $config['logger'] = $logger;
 *    $config['modules']['devel'] = vendor_name\project_name\devel\DevelModule::class;
 *
 *    return $config;
 *  });
 *
 */

// Get rid of global variable
use atsilex\module\queue\QueueModule;
use atsilex\module\system\SystemModule;

return call_user_func(function () {
    global $loader;

    !defined('APP_ROOT') && define('APP_ROOT', __DIR__);

    return [
        'debug'               => true,
        'app.root'            => APP_ROOT,
        'db.options'          => ['driver' => 'pdo_sqlite', 'path' => APP_ROOT . '/files/app.db'],

        # Modules
        # ---------------------
        'modules'             => [
            'queue'  => QueueModule::class,
            'system' => SystemModule::class,
        ],

        # The front-end configurations
        # ---------------------

        # Template params
        'site_name'           => 'Project Name', # Also used in Console
        'site_version'        => 'dev',          # Also used in Console
        'site_footer'         => '© <a href="http://www.vendor-name.com/">Vendor Name</a> ' . date('Y'),
        'site_navbar_classes' => 'navbar navbar-default navbar-fixed-top',
        'site_body_classes'   => 'body',
        'site_frontpage'      => '/hello',

        # Front-end frameworks
        'site_theme'          => '//bootswatch.com/united/bootstrap.css',
        'site_ga_code'        => getenv('SITE_GA_CODE'),
        'site_jquery'         => '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        'site_bootstrap'      => '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4',
        'site_html5shiv'      => '//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js',
        'site_respondjs'      => '//oss.maxcdn.com/respond/1.4.2/respond.min.js',

        # Twig
        'twig.form.templates' => ['bootstrap_3_horizontal_layout.html.twig'],

        # The security configuration
        # ---------------------
        // Authentication
        'security.firewalls'  => [
            'login'   => ['pattern' => '^/user/login$'],
            'default' => [
                'pattern'   => '^/admin.*$',
                'form'      => ['login_path' => '/login', 'check_path' => '/login'],
                'logout'    => ['logout_path' => '/user/logout', 'with_csrf' => true],
                'anonymous' => true,
            ]
        ],

        // Authorization
        # 'security.access_rules' => [],
    ];
});
