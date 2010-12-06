<?php
// Module -> View
if (!$no_template) {
    $tplname = $modfunc == Config::standard_modfunc ? $modname : $modfunc;

    try {
        $tpl = new TPL($tplname . '.html', $modname);
        foreach ($mod_vars as $k => $v) {
            $tpl->set($k, $v);
        }
        // We cache the data so we can save a lot of foreach runs together with if/else...
        $tmp_data = $tpl->get_data();
        $mod_content = $tpl->inline();
    } catch (Exception $ex) {}

    if (!$no_general_template) {
        try {
            $tpl = new TPL('general.js', $modname);
            $tpl->set_data($tmp_data);
            $mod_javascript = $tpl->inline();
        } catch (Exception $ex) {}

        try {
            $tpl = new TPL('general.css', $modname);
            $tpl->set_data($tmp_data);
            $mod_css = $tpl->inline();
        } catch (Exception $ex) {}

        try {
            $tpl = new TPL($tplname . '.js', $modname);
            $tpl->set_data($tmp_data);
            $mod_javascript .= $tpl->inline();
        } catch (Exception $ex) {}

        try {
            $tpl = new TPL($tplname . '.css', $modname);
            $tpl->set_data($tmp_data);
            $mod_css .= $tpl->inline();
        } catch (Exception $ex) {}
    }
}

// Front -> View
if ($no_general_template) {
    $output = $mod_content;
} else {
    try {
        $tpl = new TPL('general.css');
        foreach ($global_vars as $k => $v) {
            $tpl->set($k, $v);
        }
        $tmp_data = $tpl->get_data();
        $css = $tpl->inline();
    } catch (Exception $ex) {}

    try {
        $tpl = new TPL('general.js');
        $tpl->set_data($tmp_data);
        $javascript = $tpl->inline();
    } catch (Exception $ex) {}

    try {
        $tpl = new TPL('general.html');
        $tpl->set_data($tmp_data);
        $tpl->set('content', $mod_content);

        if (!$no_template) {
            $page_css = $css . "\n" . $mod_css;
            $page_javascript = $javascript . "\n" . $mod_js;
        } else {
            $page_css = $css;
            $page_javascript = $javascript;
        }

        if (trim($page_css) != '') {
            $tpl->set('custom_css', $page_css);
        }

        if (trim($page_javascript) != '') {
            $tpl->set('custom_js', $page_javascript);
        }

        $output = $tpl->inline();
    } catch (Exception $ex) {}
}
?>