<?php
// Simple router
if( isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] != '' ) {
    $registered_urls = array(
        'testurl' => array('test', 'testfunc'),
        'registration' => 'registration',
        'login' => 'login',
        'logout' => 'logout'
    );

    $calling_url = substr($_SERVER['REDIRECT_URL'], 1);
    if( isset($registered_urls[$calling_url]) ) {
        if( is_array($registered_urls[$calling_url]) ) {
            list($modname, $modfunc) = $registered_urls[$calling_url];
        } else {
            $modname = $registered_urls[$calling_url];
        }
    }
}
?>