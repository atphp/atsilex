<?php

namespace atsilex\module\system;

use atsilex\module\Module;
use atsilex\module\system\providers\Register;
use Pimple\Container;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO
 *
 *  Features
 *  ---------------------
 *  - Include Swift Mailer.
 *  - Hal
 *      - https://github.com/mikekelly/hal-browser
 *      - Nocarrier\Hal — https://github.com/blongden/hal
 *      - https://github.com/easybiblabs/silex-hal-provider
 *      - http://www.slideshare.net/LukeStokes/pox-to-hateoas-13077649
 *  - Support drupal navbar and/or github.com/vadikom/smartmenus
 *  - Document how to change cache backend.
 */
class SystemModule extends Module
{

    protected $machineName = 'system';
    protected $name        = 'System Module';
    protected $description = 'Implements core functions.';
    protected $routeFile   = true;

    /**
     * Created when Twig environment object is created.
     */
    const EVENT_TWIG_CREATE = 'system.twig.create';

    public function register(Container $c)
    {
        (new Register())->register($c);

        // Site front-page
        if (isset($c['site_frontpage']) && ('/' !== $c['site_frontpage'])) {
            $c->get('/', function (Container $c) {
                return $c->handle(Request::create($c['site_frontpage']));
            });
        }
    }

}
