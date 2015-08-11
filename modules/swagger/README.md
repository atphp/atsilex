Swagger module
====

This module provides (1) [Swagger UI](http://swagger.io) at /swagger and (2) Auto generate the `/swagger.json` file.

## Example

Define Swagger annotations, the module with add it to `swagger.json` response. 

```php
namespace my_module\controllers;

use vendor_name\project_name\system\controllers\BaseController;
use vendor_name\project_name\swagger\annotations as Swagger;

class HelloController extends BaseController {

    /**
     * @Swagger\Param(name="name", in="path")
     * @Swagger\Response("Hello")
     */
    public function actionGet($name) {
        return $this->json(new Hello($name));
    }

}
```
