<?php

namespace v3knet\module\swagger\controllers;

use Doctrine\Common\Annotations\AnnotationReader;
use phpDocumentor\Reflection\DocBlock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use v3knet\module\exceptions\RuntimeException;
use v3knet\module\ServiceProvider;
use v3knet\module\swagger\annotations\Method;
use v3knet\module\swagger\annotations\Param;
use v3knet\module\swagger\annotations\Response;
use v3knet\module\system\controllers\BaseController;

class SwaggerController extends BaseController
{

    /** @var array $definitions */
    private $definitions = [];

    /** @var array $metadata Doctrine entity meta data */
    private $metadata;

    public function actionGetUI()
    {
        return $this
            ->getApp()
            ->getTwig()
            ->render('@swagger/pages/swagger-ui.twig', [
                'cdn' => '//cdn.rawgit.com/swagger-api/swagger-ui/master/dist',
                'url' => $this->getApp()->url('swagger-json'),
            ]);
    }

    public function actionGet(Request $request)
    {
        /** @var RequestContext $context */
        $context = $this->getApp()['url_generator']->getContext();
        $paths = $this->getPaths();

        return $this->json([
            'swagger'     => '2.0',
            'info'        => [
                'title'       => 'Helen API',
                'description' => 'Human resource managment',
                'version'     => '1.0.0'
            ],
            'host'        => $context->getHost(),
            'schemes'     => [$context->getScheme()],
            'basePath'    => '/v1.0',
            'produces'    => ['application/json'],
            'definitions' => $this->definitions,
            'paths'       => $paths,
        ], 200);
    }

    private function getPaths()
    {
        $reader = new AnnotationReader();
        $paths = [];

        foreach ($this->getApp()['routes']->getIterator() as $key => $route) {
            if (in_array($route->getPath(), ['/swagger.json', '/'])) {
                continue;
            }

            if ($ctrl = $route->getDefaults()['_controller']) { // Path has controller
                try {
                    $this->getPath($reader, $key, $route, $ctrl, $paths);
                }
                catch (\RuntimeException $e) {
                    // Unable to add route to swagger info response.
                }
            }
        }

        return $paths;
    }

    private function getPath(AnnotationReader $reader, $key, $route, $ctrl, array &$paths)
    {
        if (!is_callable($ctrl)) {
            list($class, $method) = is_callable($ctrl) ? $ctrl : explode(':', $ctrl);
            list($module, $group, $chunks) = explode('.', trim($class), 3); // Parse @MODULE.group.chunks
            $class = $this->getApp()->getModule($module)->getMagicServiceClass($group, $chunks);
        }
        elseif (is_array($ctrl)) {
            $class = $ctrl[0];
            $method = $ctrl[1];
        }
        else {
            return; // unsupported controller
        }

        $rClass = new \ReflectionClass($class);
        $cAnnotations = $reader->getClassAnnotations($rClass);
        $rMethod = $rClass->getMethod($method);
        $annotations = $reader->getMethodAnnotations($rMethod);
        $rDoc = new DocBlock($rMethod);
        $rComment = $rMethod->getDocComment();
        $ns = "vendor_name\\project_name\\annotations\\swagger\\";
        $default = $route->getDefaults();
        $httpMethods = $route->getMethods();
        $methods = $params = [];
        $response = null;

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Param) {
                $params[] = $annotation;
            }
            else if ($annotation instanceof Response) {
                $response = $annotation;
            }
        }

        foreach ($cAnnotations as $cAnnotation) {
            if ($cAnnotation instanceof Method) {
                $methods[] = $cAnnotation;
            }
        }

        if (!$rComment && count($methods) < 1) {
            if ($class !== __CLASS__) {
                # throw new \RuntimeException("{$class}:{$method} missing doc block comment.");
            }
        }

        $info = [
            "summary"   => count($methods) && isset($classMethod) ? $classMethod->description : $rDoc->getShortDescription(),
            "responses" => [
                200       => ["description" => "", "schema" => ""],
                "default" => ["description" => "Unexpected error", "schema" => ['$ref' => "Error"]]
            ]
        ];

        // in case methods are extended, we cannot add PHP comment include
        // annotation to methods, we must use @Swagger\Method to add swagger
        // annotation. @see: src/controllers/employee/WorkController.php
        $swaggerMethod = strtolower($httpMethods[0]);
        if (count($methods) > 0) {
            foreach ($methods as $classMethod) {
                if ($classMethod->value != $method) {
                    continue;
                }

                foreach ($classMethod->params as $param) {
                    $info['parameters'][] = $param->getParam();
                }

                $this->swaggerResponse($classMethod->response, $info);
                $paths[$route->getPath()][$swaggerMethod] = $info;
            }
        }
        else {
            $info = [
                "summary"   => $rDoc->getShortDescription(),
                "responses" => [
                    200       => ["description" => "", "schema" => ""],
                    "default" => ["description" => "Unexpected error", "schema" => ['$ref' => "Error"]]
                ]
            ];

            if ($params) {
                foreach ($params as $param) {
                    $info['parameters'][] = $param->getParam();
                }
            }

            // calculate response object
            if ($response) {
                $this->swaggerResponse($response, $info, $swaggerMethod);
            }
            $paths[$route->getPath()][$swaggerMethod] = $info;
        }
    }

    /**
     * Calculate swagger response
     *
     * @param Response $response
     * @param array    $info Swagger api info to return
     */
    function swaggerResponse(Response $response, &$info, $swaggerMethod = 'get')
    {
        // shorthand response object
        // [EntityType] = { schema: { "type": "array", "items": { "$ref": "EntityType" } } }
        $def = '';
        if ($response->value) {
            if (preg_match('/\[([A-Za-z0-9_]+)\]/', $response->value, $matches)) {
                $def = $matches[1];
                $info['responses'][200]['schema']['type'] = 'array';
                $info['responses'][200]['schema']['items']['$ref'] = $def;
                $this->addDefinition($def);
            }
            else {
                $def = $response->value;
                $info['responses'][200]['schema']['$ref'] = $def;
                $this->addDefinition($def);
            }
        }
        else {
            $def = $response;
            // full response object is pass as is
            $info['responses'][200] = $def;
        }

        if ($swaggerMethod === 'post') {
            $info['parameters'] = [['name' => 'body', 'in' => 'body', 'schema' => ['$ref' => $def]]];
        }
    }

    /**
     * Add entity to definition class name.
     *
     * @param string $entity Entity name
     * @return void
     */
    private function addDefinition($entity)
    {
        $default = $this->defaultDefinition();
        if (isset($default[$entity])) {
            $this->definitions[$entity] = $default[$entity];
            return;
        }

        $meta = $this->getEntityMetaData($entity);
        foreach ($meta->fieldMappings as $field => $info) {
            $type = $info['type'];

            if ('datetime' === $type) {
                $this->definitions[$entity]['properties'][$field] = [
                    'type'   => 'string',
                    'format' => 'date-time'
                ];
            }
            elseif ('date' === $type) {
                $this->definitions[$entity]['properties'][$field] = [
                    'type'   => 'string',
                    'format' => 'date'
                ];
            }
            elseif ('json_array' === $type) {
                $this->definitions[$entity]['properties'][$field] = [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'string'
                    ]
                ];
            }
            elseif ('float' === $type) {
                $this->definitions[$entity]['properties'][$field] = [
                    'type'   => 'number',
                    'format' => 'float'
                ];
            }
            else {
                $this->definitions[$entity]['properties'][$field] = [
                    'type' => 'text' === $type ? 'string' : $type
                ];
            }
        }

        foreach ($meta->associationMappings as $field => $info) {
            $parts = explode('\\', $info['targetEntity']);
            $className = end($parts);
            $this->definitions[$entity]['properties'][$field] = ['$ref' => $className];
            if (!isset($this->definitions[$className])) {
                $this->addDefinition($className);
            }
        }
    }

    /**
     * Default swagger definition (Error, DeleteResult, ...)
     *
     * @return array
     */
    private function defaultDefinition()
    {
        return [
            'DeleteResult' => [
                'properties' => [
                    'status' => ['type' => 'string']
                ]
            ],
            'Error'        => [
                'properties' => [
                    'code'    => ['type' => 'int'],
                    'message' => ['type' => 'string']
                ]
            ]
        ];
    }

    /**
     * Get doctrine entity metadata
     *
     * @param string $entity
     * @return object
     * @throws RuntimeException
     */
    private function getEntityMetaData($entity)
    {
        if (!$this->metadata) {
            $this->metadata = $this->getApp()->getEntityManager()->getMetadataFactory()->getAllMetadata();
        }

        $matches = [];
        foreach ($this->metadata as $meta) {
            if (preg_match("/$entity$/", $meta->name)) {
                $matches[$meta->name] = $meta;
            }
        }

        // same class name appeared multiple time in difference namespaces
        if (count($matches) > 1) {
            throw new RuntimeException();
        }

        // cannot find entity
        if (count($matches) === 0) {
            throw new RuntimeException("Entity $entity cannot be found.");
        }

        return reset($matches);
    }

}
