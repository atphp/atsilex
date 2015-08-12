<?php

namespace atsilex\module\system\providers;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\SerializerBuilder;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * By https://github.com/jdesrosiers/silex-jms-serializer-provider
 */
class JmsSerializerServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Container $c)
    {
        $c['serializer.namingStrategy.separator'] = null;
        $c['serializer.namingStrategy.lowerCase'] = null;
        $c['serializer.builder'] = function (Container $c) {
            $builder = SerializerBuilder::create()->setDebug($c['debug']);
            isset($c['serializer.annotationReader']) && $builder->setAnnotationReader($c['serializer.annotationReader']);
            isset($c['serializer.cacheDir']) && $builder->setCacheDir($c['serializer.cacheDir']);
            isset($c['serializer.configureHandlers']) && $builder->configureHandlers($c['serializer.configureHandlers']);
            isset($c['serializer.configureListeners']) && $builder->configureListeners($c['serializer.configureListeners']);
            isset($c['serializer.objectConstructor']) && $builder->setObjectConstructor($c['serializer.objectConstructor']);
            isset($c['serializer.namingStrategy']) && $this->namingStrategy($c, $builder);
            isset($c['serializer.serializationVisitors']) && $this->setSerializationVisitors($c, $builder);
            isset($c['serializer.deserializationVisitors']) && $this->setDeserializationVisitors($c, $builder);
            isset($c['serializer.includeInterfaceMetadata']) && $builder->includeInterfaceMetadata($c['serializer.includeInterfaceMetadata']);
            isset($c['serializer.metadataDirs']) && $builder->setMetadataDirs($c['serializer.metadataDirs']);
            return $builder;
        };

        $c['serializer'] = function (Container $c) {
            return $c['serializer.builder']->build();
        };

        isset($c['serializer.srcDir']) && AnnotationRegistry::registerAutoloadNamespace('JMS\Serializer\Annotation', $c['serializer.srcDir']);
    }

    /**
     * Set the serialization naming strategy
     *
     * @param Container         $c
     * @param SerializerBuilder $builder
     * @throws ServiceUnavailableHttpException
     */
    protected function namingStrategy(Container $c, SerializerBuilder $builder)
    {
        if ($c['serializer.namingStrategy'] instanceof PropertyNamingStrategyInterface) {
            $namingStrategy = $c['serializer.namingStrategy'];
        }
        else {
            switch ($c['serializer.namingStrategy']) {
                case 'IdenticalProperty':
                    $namingStrategy = new IdenticalPropertyNamingStrategy();
                    break;
                case 'CamelCase':
                    $namingStrategy = new CamelCaseNamingStrategy(
                        $c['serializer.namingStrategy.separator'],
                        $c['serializer.namingStrategy.lowerCase']
                    );
                    break;
                default:
                    throw new ServiceUnavailableHttpException(
                        null,
                        "Unknown property naming strategy '" . $c['serializer.namingStrategy'] . "'.  " .
                        "Allowed values are 'IdenticalProperty' or 'CamelCase'"
                    );
            }
            $namingStrategy = new SerializedNameAnnotationStrategy($namingStrategy);
        }
        $builder->setPropertyNamingStrategy($namingStrategy);
    }

    /**
     * Override default serialization vistors
     *
     * @param Container         $c
     * @param SerializerBuilder $builder
     */
    protected function setSerializationVisitors(Container $c, SerializerBuilder $builder)
    {
        $builder->addDefaultSerializationVisitors();
        foreach ($c['serializer.serializationVisitors'] as $format => $visitor) {
            $builder->setSerializationVisitor($format, $visitor);
        }
    }

    /**
     * Override default deserialization visitors
     *
     * @param Container         $c
     * @param SerializerBuilder $builder
     */
    protected function setDeserializationVisitors(Container $c, SerializerBuilder $builder)
    {
        $builder->addDefaultDeserializationVisitors();
        foreach ($c['serializer.deserializationVisitors'] as $format => $visitor) {
            $builder->setDeserializationVisitor($format, $visitor);
        }
    }

}
