<?php
abstract class FW
{
    public static function get_ajax_link() {
        return html_entity_decode(self::get_mod_link(func_get_args()));
    }

    public static function get_mod_link() {
        $modlink = '/index.php?';

        // TODO: Add language params
        $lang = 'lang=de&amp;';

        if (func_num_args() > 2) {
            $mod = func_get_arg(0);
            $func = func_get_arg(1);
            $params = func_get_arg(2);
            if (is_array($params)) {
                $params = implode('&amp;', $params);
            }
            $modlink .= "mod=$mod&amp;func=$func&amp;$params";
        } elseif (func_num_args() > 1) {
            $mod = func_get_arg(0);
            $func = func_get_arg(1);
            $modlink .= "mod=$mod&amp;func=$func";
        } elseif (func_num_args() > 0) {
            $mod = func_get_arg(0);
            $modlink .= "mod=$mod";
        }

        return $modlink;
    }

    public static function set_no_cache() {
        self::add_header('Cache-Control: private, no-cache, max-age=0, must-revalidate');
        self::add_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    public static function get_parameter($k) {
        $cli_mode = PHP_SAPI == 'cli' ? true : false;

        if (!$cli_mode) {
            $ret = $_REQUEST[$key];
        } else {
            $ret = $_ENV[$key];
        }
    }
    
    public static function add_header($k, $v) {
        header($k . ': ' . $v);
    }
}
?>