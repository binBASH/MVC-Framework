<?php
session_save_path('../tmp/sessions');
session_start();

// Only check allowed methods for external modules
if (!$is_plugin) {
    $app->set_var('error_msg', null);
    if ( strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0
        && method_exists($app, 'check_parameters')
        && $app->check_parameters() ) {

        if (has_parameter('edit_id') && intval(get_parameter('edit_id')) > 0) {
            if (method_exists($app, 'save_edited')) {
                $app->save_edited();
            }
        } else {
            if (method_exists($app, 'save_new')) {
                $app->save_new();
            }
        }

    }

    $allowed_methods = array_flip($app->get_allowed_methods());
    if (!isset($allowed_methods[$modfunc]))
        throw new Exception('Method not allowed (' . $modname . ' -> ' . $modfunc . ')');
}
?>