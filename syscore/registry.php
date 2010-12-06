<?php
abstract class Registry
{
    private static $registry = array();

    public static function set($key, $value) {
        if (!isset(self::$registry[$key])) {
            self::$registry[$key] = $value;
            return true;
        } else {
            throw new Exception('Unable to set variable `' . $key . '`. It was already set.');
        }
    }

    public static function get($key) {
        if (isset(self::$registry[$key])) {
            return self::$registry[$key];
        }
        return null;
    }

    public static function get_all() {
        return self::$registry;
    }

    public static function remove($key) {
        if (isset(self::$registry[$key])) {
            unset(self::$registry[$key]);
            return true;
        }
        return false;
    }

    public static function remove_all() {
        self::$registry = array();
        return true;
    }
}
?>