<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale;

class System {
    public static function toCamelCase($str, $capitalizeFirst = false){
        $str = str_replace('"', '', $str);
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
        return $capitalizeFirst ? $str : lcfirst($str);
    }
} 