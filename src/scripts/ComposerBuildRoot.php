<?php

namespace vendor_name\project_name\scripts;

class ComposerBuildRoot
{

    public function execute()
    {
        $root = realpath(__DIR__ . '/../../');

        // Get root composer, reset it
        $composer = json_decode(file_get_contents($root . '/composer.json'), true);
        $composer['require'] = ['php' => '>=5.5'];
        $composer['require-dev'] = ['symfony\/var-dumper' => '^2.7.0'];
        $composer['autoload'] = ['psr-4' => ['vendor_name\\project_name\\' => 'src']];

        foreach (glob($root . '/modules/*/composer.json') as $path) {
            $this->merge($composer, $path, $root);
        }

        foreach (array_keys($composer['require']) as $name) {
            if (0 === strpos($name, 'v3knet/')) {
                unset($composer['require'][$name]);
            }
        }

        $composer = json_encode($composer, JSON_PRETTY_PRINT);
        file_put_contents($root . '/composer.json', $composer);
    }

    private function merge(&$composer, $path, $baseDir)
    {
        $dir = dirname($path);
        $prefix = substr($dir, strlen($baseDir . '/'));
        $info = json_decode(file_get_contents($path), true);

        // Prepend dir
        foreach (['autoload', 'autoload-dev'] as $key) {
            if (isset($info[$key])) {
                foreach ($info[$key] as $strategy => $rules) {
                    foreach ($rules as $ns => $path) {
                        $info[$key][$strategy][$ns] = trim($prefix . '/' . trim($path, './'), '/');
                    }
                }
            }
        }

        // Merge parts
        foreach (['require', 'require-dev', 'autoload'] as $key) {
            $composer[$key] = isset($composer[$key]) ? $composer[$key] : [];
            if (!empty($info[$key])) {
                $composer[$key] = $this->mergeDeepArray($composer[$key], $info[$key]);
            }
        }

        if (!empty($info['autoload']['psr-4'])) {
            $composer['autoload']['psr-4'] += array_merge_recursive($composer['autoload']['psr-4'], $info['autoload']['psr-4']);
        }
    }

    private function mergeDeepArray()
    {
        $args = func_get_args();
        return $this->doMergeDeepArray($args);
    }

    private function doMergeDeepArray($arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result [] = $value;
                }
                // Recurse when both values are arrays.
                elseif (isset($result [$key]) && is_array($result [$key]) && is_array($value)) {
                    $result [$key] = $this->doMergeDeepArray([$result [$key], $value]);
                }
                // Otherwise, use the latter value, overriding any previous value.
                else {
                    $result [$key] = $value;
                }
            }
        }

        return $result;
    }

}

(new ComposerBuildRoot)->execute();
