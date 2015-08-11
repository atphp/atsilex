<?php

namespace vendor_name\project_name\scripts;

/**
 * @TODO TBD
 */
class ReplaceSourceToken extends BaseScript
{

    private $tokens = [
        'DOMAIN_NAME'  => 'www.mycompany.com',
        'VENDOR_NAME'  => 'My Company',
        'VendorName'   => 'my-company',
        'vendor_name'  => 'mycompany',
        'PROJECT_NAME' => 'My Project',
        'ProjectName'  => 'my-project',
        'project_name' => 'my_project'
    ];

    public function execute()
    {
        $file = $this->app->getAppRoot() . '/files/.not_installed';
        if (!is_file($file)) {
            return;
        }

        foreach ($this->tokens as $find => $replace) {
            $this->doReplaceToken($find, $replace);
        }
    }

    private function doReplaceToken($find, $replace)
    {
        throw new \RuntimeException('@TODO: http://j.mp/1SV3MWJ');
    }

}
