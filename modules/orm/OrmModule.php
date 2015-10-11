<?php

namespace atsilex\module\orm;

use atsilex\module\Module;
use atsilex\module\system\ModularApp;
use atsilex\module\orm\providers\DoctrineOrmServiceProvider;
use Pimple\Container;

/**
 * @TODO: Provide UI to build custom entity.
 * @TODO: Provide REST services for ORM entities — https://github.com/stanlemon/rest-bundle
 */
class OrmModule extends Module
{
    protected $machineName = 'orm';
    protected $name        = 'ORM';

    /**
     * @param ModularApp $c
     */
    public function register(Container $c)
    {
        $c['orm.proxies.dir'] = function (Container $c) {
            return $c['app.root'] . '/files/proxies';
        };

        $c['orm.mappings'] = function (Container $c) {
            $ormMappings = [];

            foreach ($c->getModules() as $module) {
                if ($mappings = $c->getModule($module)->getEntityMappings($c)) {
                    $ormMappings = array_merge($ormMappings, $mappings);
                }
            }

            return $ormMappings;
        };

        $c->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $c['orm.proxies.dir'],
            'orm.em.options'  => ['mappings' => $c['orm.mappings']],
        ]);
    }
}
