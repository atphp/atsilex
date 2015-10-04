<?php

namespace atsilex\module\system\providers;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DoctrineOrmServiceProvider implements ServiceProviderInterface
{
    /**
     * Get default ORM configuration settings.
     *
     * @param Container $c
     *
     * @return array
     */
    protected function getOrmDefaults()
    {
        return array(
            'orm.proxies_dir'                 => __DIR__ . '/../../../../../../../../cache/doctrine/proxies',
            'orm.proxies_namespace'           => 'DoctrineProxy',
            'orm.auto_generate_proxies'       => true,
            'orm.custom.functions.string'     => [],
            'orm.custom.functions.numeric'    => [],
            'orm.custom.functions.datetime'   => [],
            'orm.custom.hydration_modes'      => [],
            'orm.class_metadata_factory_name' => ClassMetadataFactory::class,
            'orm.default_repository_class'    => EntityRepository::class,
        );
    }

    public function register(Container $c)
    {
        foreach ($this->getOrmDefaults() as $key => $value) {
            if (!isset($c[$key])) {
                $c[$key] = $value;
            }
        }

        $c['orm.em.default_options'] = array(
            'connection' => 'default',
            'mappings'   => [],
            'types'      => []
        );

        $c['orm.cache.configurer'] = $c->protect(function ($name, Configuration $config, $options) use ($c) {
            $config->setMetadataCacheImpl($c['orm.cache.locator']($name, 'metadata', $options));
            $config->setQueryCacheImpl($c['orm.cache.locator']($name, 'query', $options));
            $config->setResultCacheImpl($c['orm.cache.locator']($name, 'result', $options));
            $config->setHydrationCacheImpl($c['orm.cache.locator']($name, 'hydration', $options));
        });

        $c['orm.ems.options.initializer'] = $c->protect(function () use ($c) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($c['orm.ems.options'])) {
                $c['orm.ems.options'] = array('default' => isset($c['orm.em.options']) ? $c['orm.em.options'] : []);
            }

            $tmp = $c['orm.ems.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($c['orm.em.default_options'], $options);

                if (!isset($c['orm.ems.default'])) {
                    $c['orm.ems.default'] = $name;
                }
            }
            $c['orm.ems.options'] = $tmp;
        });

        $c['orm.em_name_from_param_key'] = $c->protect(function ($paramKey) use ($c) {
            $c['orm.ems.options.initializer']();

            if (isset($c[$paramKey])) {
                return $c[$paramKey];
            }

            return $c['orm.ems.default'];
        });

        $c['orm.ems'] = function ($c) {
            $c['orm.ems.options.initializer']();

            $ems = new Container();
            foreach ($c['orm.ems.options'] as $name => $options) {
                if ($c['orm.ems.default'] === $name) {
                    // we use shortcuts here in case the default has been overridden
                    $config = $c['orm.em.config'];
                }
                else {
                    $config = $c['orm.ems.config'][$name];
                }

                $ems[$name] = function ($ems) use ($c, $options, $config) {
                    return EntityManager::create(
                        $c['dbs'][$options['connection']],
                        $config,
                        $c['dbs.event_manager'][$options['connection']]
                    );
                };
            }

            return $ems;
        };

        $c['orm.ems.config'] = function ($c) {
            $c['orm.ems.options.initializer']();

            $configs = new Container();
            foreach ($c['orm.ems.options'] as $name => $options) {
                $config = new Configuration;

                $c['orm.cache.configurer']($name, $config, $options);

                $config->setProxyDir($c['orm.proxies_dir']);
                $config->setProxyNamespace($c['orm.proxies_namespace']);
                $config->setAutoGenerateProxyClasses($c['orm.auto_generate_proxies']);

                $config->setCustomStringFunctions($c['orm.custom.functions.string']);
                $config->setCustomNumericFunctions($c['orm.custom.functions.numeric']);
                $config->setCustomDatetimeFunctions($c['orm.custom.functions.datetime']);
                $config->setCustomHydrationModes($c['orm.custom.hydration_modes']);

                $config->setClassMetadataFactoryName($c['orm.class_metadata_factory_name']);
                $config->setDefaultRepositoryClassName($c['orm.default_repository_class']);

                $config->setEntityListenerResolver($c['orm.entity_listener_resolver']);
                $config->setRepositoryFactory($c['orm.repository_factory']);

                $config->setNamingStrategy($c['orm.strategy.naming']);
                $config->setQuoteStrategy($c['orm.strategy.quote']);

                $chain = $c['orm.mapping_driver_chain.locator']($name);

                foreach ((array) $options['mappings'] as $entity) {
                    if (!is_array($entity)) {
                        throw new \InvalidArgumentException(
                            "The 'orm.em.options' option 'mappings' should be an array of arrays."
                        );
                    }

                    if (isset($entity['alias'])) {
                        $config->addEntityNamespace($entity['alias'], $entity['namespace']);
                    }

                    switch ($entity['type']) {
                        case 'annotation':
                            $useSimpleAnnotationReader =
                                isset($entity['use_simple_annotation_reader'])
                                    ? $entity['use_simple_annotation_reader']
                                    : true;
                            $driver = $config->newDefaultAnnotationDriver((array) $entity['path'], $useSimpleAnnotationReader);
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        case 'yml':
                            $driver = new YamlDriver($entity['path']);
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        case 'simple_yml':
                            $driver = new SimplifiedYamlDriver(array($entity['path'] => $entity['namespace']));
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        case 'xml':
                            $driver = new XmlDriver($entity['path']);
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        case 'simple_xml':
                            $driver = new SimplifiedXmlDriver(array($entity['path'] => $entity['namespace']));
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        case 'php':
                            $driver = new StaticPHPDriver($entity['path']);
                            $chain->addDriver($driver, $entity['namespace']);
                            break;
                        default:
                            throw new \InvalidArgumentException(sprintf('"%s" is not a recognized driver', $entity['type']));
                            break;
                    }
                }
                $config->setMetadataDriverImpl($chain);

                foreach ((array) $options['types'] as $typeName => $typeClass) {
                    if (Type::hasType($typeName)) {
                        Type::overrideType($typeName, $typeClass);
                    }
                    else {
                        Type::addType($typeName, $typeClass);
                    }
                }

                $configs[$name] = $config;
            }

            return $configs;
        };

        $c['orm.mapping_driver_chain.locator'] = $c->protect(function ($name = null) use ($c) {
            $c['orm.ems.options.initializer']();

            if (null === $name) {
                $name = $c['orm.ems.default'];
            }

            $cacheInstanceKey = 'orm.mapping_driver_chain.instances.' . $name;
            if (isset($c[$cacheInstanceKey])) {
                return $c[$cacheInstanceKey];
            }

            return $c[$cacheInstanceKey] = $c['orm.mapping_driver_chain.factory']($name);
        });

        $c['orm.mapping_driver_chain.factory'] = $c->protect(function ($name) use ($c) {
            return new MappingDriverChain;
        });

        $c['orm.add_mapping_driver'] = $c->protect(function (MappingDriver $mappingDriver, $namespace, $name = null) use ($c) {
            $c['orm.ems.options.initializer']();

            if (null === $name) {
                $name = $c['orm.ems.default'];
            }

            /** @var MappingDriverChain $driverChain */
            $driverChain = $c['orm.mapping_driver_chain.locator']($name);
            $driverChain->addDriver($mappingDriver, $namespace);
        });

        $c['orm.strategy.naming'] = function ($c) {
            return new DefaultNamingStrategy;
        };

        $c['orm.strategy.quote'] = function ($c) {
            return new DefaultQuoteStrategy;
        };

        $c['orm.entity_listener_resolver'] = function ($c) {
            return new DefaultEntityListenerResolver;
        };

        $c['orm.repository_factory'] = function ($c) {
            return new DefaultRepositoryFactory;
        };

        $c['orm.em'] = function ($c) {
            $ems = $c['orm.ems'];

            return $ems[$c['orm.ems.default']];
        };

        $c['orm.em.config'] = function ($c) {
            $configs = $c['orm.ems.config'];

            return $configs[$c['orm.ems.default']];
        };
    }
}
