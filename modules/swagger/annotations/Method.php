<?php

namespace atsilex\module\swagger\annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Method extends Annotation
{

    /**
     * @var array<Param>
     */
    public $params;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var string
     */
    public $description;

}
