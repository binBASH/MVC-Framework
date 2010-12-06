<?php
$render_start = microtime(true);

// Autoloader for class files
function __autoload($class_name) {
    $class = strtolower($class_name);
    if (file_exists("../syscore/$class.php")) {
        include("../syscore/$class.php");
    } elseif (file_exists("../datamodels/$class.php")) {
        include("../datamodels/$class.php");
    } elseif (file_exists("../apps/$class/$class.php")) {
        include("../apps/$class/$class.php");
    } else {
        throw new Exception("Cannot load class $class_name");
    }
}

// We save one include by putting Controller Class directly into main program file
class Controller
{
    private $view_type;

    public function __construct($view_type='html') {
        $this->view_type = $view_type;
    }

    public function mod_exec($modname, $modfunc, $is_plugin) {
        include('../syscore/router.php');

        // Normally you can't catch exceptions thrown in __autoload function
        // This is a workaround because it works with class_exists
        try {
            class_exists($modname);
        } catch (Exception $e) {
            $modname = Config::standard_modname;
            try {
                class_exists($modname);
            } catch (Exception $e) {
                Debug::out('Critical: Class does not exist (' . $modname . ')');
                die();
            }
        }

        ob_start();
        $app = new $modname();

        // Custom code which is run before every module
        include('../syscore/bootstrap.php');

        if (!method_exists($app, $modfunc)) {
            $modfunc = Config::standard_modfunc;
            if (!method_exists($app, $modfunc)) {
                throw new Exception('Method not found (' . $modname . ' -> ' . $modfunc . ')');
            }
        }

        $app->$modfunc();
        $mod_content = ob_get_contents();
        ob_end_clean();

        $global_vars = $app->get_global_vars();
        $mod_vars = $app->get_vars();

        $no_template = $app->get_no_template();
        $no_general_template = $app->get_no_general_template();

        $output = '';

        // Supported views
        if( $this->view_type == 'html' ) {
            include('../syscore/view_html.php');
        }

        return $output;
    }
}

// #################   Main Code

// Debug::set_mode('echo');

// Load project configuration
require('../configs/democonfig.php');
require('../syscore/fwfunctions.php');

$db = DB::get_connection(Config::db_driver, Config::db_host, Config::db_username, Config::db_password, Config::db_name);
Registry::set('dbconn', $db);

$modname = has_parameter('mod') ? get_parameter('mod') : Config::standard_modname;
$modfunc = has_parameter('func') ? get_parameter('func') : Config::standard_modfunc;

$page = new Controller();
$content = $page->mod_exec($modname, $modfunc, false);
echo str_replace('### render_time ###', sprintf('Page Generation: %0.8f secs', microtime(true) - $render_start), $content);
?>