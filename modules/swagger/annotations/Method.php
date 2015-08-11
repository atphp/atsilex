<?php

namespace v3knet\module\swagger\annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;

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
