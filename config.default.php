<?php

/**
 * NOTES: Dont edit this file directly, it will be overriden on composer install/update.
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
 * @TODO: Let modules provide default config.
 */

// Get rid of global variable
use atsilex\module\queue\QueueModule;
use atsilex\module\system\ModularApp;

return call_user_func(function () {
    global $loader;

    !defined('APP_ROOT') && define('APP_ROOT', __DIR__);

    // Default timezone
    date_default_timezone_set('UTC');

    return [
        'app.class'                  => ModularApp::class,
        'debug'                      => true,
        'app.root'                   => APP_ROOT,
        'db.options'                 => ['driver' => 'pdo_sqlite', 'path' => APP_ROOT . '/files/app.db'],

        # Modules — Select modules for the application.
        # ---------------------
        'modules'                    => [
            'queue' => QueueModule::class
        ],

        # The front-end configurations
        # ---------------------

        # Template params
        'site_name'                  => 'VỢ ❤ CON',    # Used in Console
        'site_version'               => '0.1', # Used in Console
        'site_footer'                => '© <a href="http://www.v3k.net/">Andy Truong</a> ' . date('Y'),
        'site_navbar_classes'        => 'navbar navbar-default navbar-fixed-top',
        'site_body_classes'          => 'body',
        'site_frontpage'             => '/hello',
        'site_ga_code'               => 'UA-1234567-890',
        'site_menu'                  => [
            'primary'   => [
                '<a href="/">Home</a>',
            ],
            'secondary' => [],
        ],

        # Front-end assets
        'site_theme'                 => '//maxcdn.bootstrapcdn.com/bootswatch/3.3.5/united/bootstrap.min.css',
        'site_jquery'                => '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        'site_bootstrap'             => '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5',
        'site_html5shiv'             => '//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js',
        'site_respondjs'             => '//oss.maxcdn.com/respond/1.4.2/respond.min.js',
        'site_assets_prefix'         => '//cdn.rawgit.com/v3knet/system-module/0.1/resources/default-app/public',

        # Twig
        'twig.form.templates'        => ['bootstrap_3_horizontal_layout.html.twig'],

        # Queue
        # ---------------------
        'queue.consumer.throw_error' => true,

        # The security configuration
        # ---------------------
        // Authentication
        'security.firewalls'         => [
            'login'   => ['pattern' => '^/user/login$'],
            'default' => [
                'pattern'   => '^/admin.*$',
                'form'      => ['login_path' => '/login', 'check_path' => '/login'],
                'logout'    => ['logout_path' => '/user/logout', 'with_csrf' => true],
                'anonymous' => true,
            ]
        ],

        # Performance
        # ---------------------
        'cache.default'              => [
            'driver' => 'filesystem',
            'path'   => APP_ROOT . '/files/cache',
        ],
        'cache.magic_services'       => true,

        // Authorization
        # 'security.access_rules' => [],

        # @TODO: Brand
        # ---------------------
        'vendor_machine_name'        => 'v3k',
    ];
});
