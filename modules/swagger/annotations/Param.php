<?php

namespace v3knet\module\swagger\annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;

/**
 * @Annotation
 * @Target("ALL")
 */
class Param extends Annotation
{

    /**
     * @var string
     */
    public $name;

    /**
     * @Enum({"path", "body"})
     */
    public $in = 'path';

    /**
     * @Enum({"string", "number", "integer", "boolean", "boolean"})
     */
    public $type;

    /**
     * @var array
     */
    public $items;

    /**
     * @var boolean
     */
    public $required;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $allowEmptyValue;

    /**
     * @var string
     */
    public $ref;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $schema;

    /**
     * @return array
     */
    public function getParam()
    {
        $vars = get_object_vars($this);

        if ($vars['in'] === 'path' && empty($vars['type'])) {
            $vars['type'] = 'string';
        }

        if (!empty($vars['value']) && empty($vars['name'])) {
            $vars['name'] = $vars['value'];
            unset($vars['value']);
        }

        return $vars;
    }

}
