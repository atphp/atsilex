<?php

namespace atsilex\scripts;

/**
 * Simple script to merge core-modules's composer files to master one.
 */
class ComposerBuildRoot
{

    public function execute()
    {
        $root = realpath(__DIR__ . '/../');

        // Get root composer, reset it
        $composer = json_decode(file_get_contents($root . '/composer.json'), true);
        $composer['require'] = ['php' => '>=5.5'];
        $composer['require-dev'] = ['symfony/var-dumper' => '^2.7.0'];
        $composer['autoload'] = ['psr-4' => ['atsilex\\' => './']];
        $composer['suggest'] = [];

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
        foreach (['require', 'require-dev', 'autoload', 'suggest'] as $key) {
            $composer[$key] = isset($composer[$key]) ? $composer[$key] : [];
            if (!empty($info[$key])) {
                $composer[$key] = $this->mergeArray([$composer[$key], $info[$key]]);
            }
        }

        if (!empty($info['autoload']['psr-4'])) {
            $composer['autoload']['psr-4'] += array_merge_recursive($composer['autoload']['psr-4'], $info['autoload']['psr-4']);
        }
    }

    private function mergeArray($arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_integer($key)) {
                    $result[] = $value;
                }
                elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = $this->mergeArray([$result[$key], $value]);
                }
                else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

}

(new ComposerBuildRoot)->execute();
