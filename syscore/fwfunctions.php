<?php
function get_ajax_link() {
    return html_entity_decode(self::get_mod_link(func_get_args()));
}

function get_mod_link() {
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

function set_no_cache() {
    add_header('Cache-Control', 'private, no-cache, max-age=0, must-revalidate');
    add_header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
}

function has_parameter($k) {
    $params = PHP_SAPI == 'cli' ? $_ENV : $_REQUEST;
    return isset($params[$k]);
}

function get_parameter($k) {
    $params = PHP_SAPI == 'cli' ? $_ENV : $_REQUEST;
    return $params[$k];
}

function set_session_parameter($k, $v) {
    $_SESSION[$k] = $v;
}

function has_session_parameter($k) {
    return isset($_SESSION[$k]);
}

function get_session_parameter($k) {
    return $_SESSION[$k];
}

function add_header($k, $v) {
    header($k . ': ' . $v);
}

function redirect($mod, $func = false) {
    $to = !$func ? get_mod_link($mod) : get_mod_link($mod, $func);
    add_header('Location', $to);
    exit;
}

function get_hash($s, $raw = false) {
    return sha1($s . Config::salt, $raw);
}

function translated($k) {
    return isset($translations['k']) ? $translations['k'] : $k;
}
?>