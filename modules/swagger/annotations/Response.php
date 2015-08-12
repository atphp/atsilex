<?php

namespace atsilex\module\swagger\annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("ALL")
 */
class Response extends Annotation
{

    /**
     * @var string
     */
    public $description;

    /**
     * @var mixed
     */
    public $schema;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var array
     */
    public $examples;

    /**
     * @var string
     */
    public $method;

}
