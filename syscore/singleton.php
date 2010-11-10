<?php
class Singleton   // Used to make sure that classes can be instantiated only once
{
    static private $instances = array();

    static public function get_instance($className) {
        if (!isset(self::$instances[$className])) {
            $arg = func_num_args() > 1 ? func_get_arg(1) : null;
            self::$instances[$className] = new $className($arg);
        }
        return self::$instances[$className];
    }

    private function __construct() {}
    private function __clone() {}
}
?>