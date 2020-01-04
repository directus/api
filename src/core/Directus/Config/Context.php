<?php

namespace Directus\Config;

/**
 * Config context interface.
 */
class Context
{
    /**
     * Source.
     *
     * @param mixed $source
     */
    public static function from_map($source)
    {
        $target = [];
        ksort($source);
        foreach ($source as $key => $value) {
            self::expand($target, explode('_', strtolower($key)), $value);
        }
        self::normalize($target);

        return $target;
    }

    /**
     * Create.
     */
    public static function from_env()
    {
        if (empty($_ENV)) {
            throw new \Error('No environment variables available. Check php_ini "variables_order" value.');
        }

        return self::from_map($_ENV);
    }

    /**
     * Loads variables from PHP file.
     *
     * @param mixed $file
     */
    public static function from_file($file)
    {
        return require $file;
    }

    /**
     * Loads variables from PHP file.
     *
     * @param mixed $array
     */
    public static function from_array($array)
    {
        return $array;
    }

    /**
     * Loads variables from JSON file.
     *
     * @param mixed $file
     */
    public static function from_json($file)
    {
        return json_decode(file_get_contents($file));
    }

    /**
     * Transforms an array of strings into a complex object.
     *
     * @example
     *  $obj = Context::expand(['a', 'b', 'c'], 12345);
     *  $obj == [
     *    'a' => [
     *      'b' => [
     *        'c' => 12345
     *      ]
     *    ]
     *  ];
     *
     * @param mixed $target
     * @param mixed $path
     * @param mixed $value
     */
    private static function expand(&$target, $path, $value)
    {
        $segment = array_shift($path);
        if (0 === \count($path)) { // leaf
            if (!\is_array($target)) {
                // TODO: raise warning - overwriting value
                $target = [];
            }
            if (\array_key_exists($segment, $target)) {
                // TODO: raise warning - overwriting group
            }
            $target[$segment] = $value;

            return;
        }
        if (!isset($target[$segment])) {
            $target[$segment] = [];
        }
        if (!\is_array($target[$segment])) {
            $target[$segment] = [];
        }
        self::expand($target[$segment], $path, $value);
    }

    /**
     * Normalize the array indexes.
     *
     * @param mixed $target
     */
    private static function normalize(&$target)
    {
        if (!\is_array($target)) {
            return;
        }

        $sort = false;
        foreach ($target as $key => $value) {
            self::normalize($target[$key]);
            $sort |= is_numeric($key);
        }

        if ($sort) {
            // TODO: which one?
            sort($target, SORT_NUMERIC);
            // vs.
            //$target = array_values($target);
        }
    }
}
