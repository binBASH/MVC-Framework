<?php
$render_start = microtime(true);

// Autoloader for class files
function __autoload($class_name) {
	$class = strtolower($class_name);
	if( file_exists("../syscore/$class.php") ) {
		include("../syscore/$class.php");
	} elseif( file_exists("../datamodels/$class.php") ) {
		include("../datamodels/$class.php");
	} elseif( file_exists("../apps/$class/$class.php") ) {
		include("../apps/$class/$class.php");
	} else {
		throw new Exception("Cannot load class $class_name");
	}
}

// We save one include by putting Controller Class directly into main program file
class Controller
{
	public function mod_exec($modname, $modfunc, $is_plugin) {
        # Normally you can't catch exceptions thrown in __autoload function
        # This is a workaround because it works with class_exists
    	try {
    		class_exists($modname);
    	} catch( Exception $e ) {
    		$modname = 'home';
    		try {
    			class_exists($modname);
    		} catch( Exception $e ) {
    			Debug::out('Critical: Class does not exist (' . $modname . ')');
                die();
    		}
        }

        ob_start();
    	$app = new $modname();
		
    	// Only check allowed methods for external modules
    	if( !$is_plugin ) {
			$allowed_methods = array_flip($app->get_allowed_methods());
			if( !isset($allowed_methods[$modfunc]) ) {
	    		throw new Exception('Method not allowed (' . $modname . ' -> ' . $modfunc . ')');    		
			}
    	}
		
		if( !method_exists($app, $modfunc) ) {
			$modfunc = 'run';
			if( !method_exists($app, $modfunc) ) {
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
		
		// Module -> View
		if( !$no_template ) {
	        try {
	            $tpl = new TPL($modfunc . '.html', $modname);
	            foreach( $mod_vars as $k => $v ) {
					$tpl->set($k, $v);
				}
				// We cache the data so we can save a lot of foreach runs together with if/else... 
				$tmp_data = $tpl->get_data();
				$mod_content = $tpl->inline();
	        } catch( Exception $ex ) {}
		
			if( !$no_general_template ) {
				try {
		            $tpl = new TPL('general.js', $modname);
					$tpl->set_data($tmp_data);
					$mod_javascript = $tpl->inline();
				} catch( Exception $ex ) {}
		        
		        try {
		    		$tpl = new TPL('general.css', $modname);
					$tpl->set_data($tmp_data);
		    		$mod_css = $tpl->inline();
		        } catch( Exception $ex ) {}
		        
		        try {
		            $tpl = new TPL($modfunc . '.js', $modname);
					$tpl->set_data($tmp_data);
					$mod_javascript .= $tpl->inline();
		        } catch( Exception $ex ) {}
		        
		        try {
		            $tpl = new TPL($modfunc . '.css', $modname);
					$tpl->set_data($tmp_data);
		            $mod_css .= $tpl->inline();
		        } catch( Exception $ex ) {}
			}
		}
		
		// Front -> View
		if( $no_general_template ) {
			$page_code = $mod_content;
		} else {
	        try {
	    		$tpl = new TPL('general.css');
				foreach( $global_vars as $k => $v ) {
					$tpl->set($k, $v);
				}
				$tmp_data = $tpl->get_data();
	    		$css = $tpl->inline();
	        } catch( Exception $ex ) {}
	        
	        try {
	            $tpl = new TPL('general.js');
	            $tpl->set_data($tmp_data);
	            $javascript = $tpl->inline();
	        } catch( Exception $ex ) {}
	
	        try {
	            $tpl = new TPL('general.html');
	            $tpl->set_data($tmp_data);
	            $tpl->set('content', $mod_content);
	            
	            if( !$no_template ) {
		            $page_css = $css . "\n" . $mod_css;        
		            $page_javascript = $javascript . "\n" . $mod_js;
	            } else {
	            	$page_css = $css;
	            	$page_javascript = $javascript;
	            }
	            
	            if( trim($page_css) != '' ) {
	                $tpl->set('custom_css', $page_css);
	            }
	            
	            if( trim($page_javascript) != '' ) {
	                $tpl->set('custom_js', $page_javascript);
	            }
	            
	            $page_code = $tpl->inline();	            
	        } catch( Exception $ex ) {}
		}
		return $page_code;
	}
}

// #################   Main Code

// Debug::set_mode('echo');

// Load project configuration
require('../configs/democonfig.php');

$modname = isset($_GET['mod']) ? $_GET['mod'] : 'home';
$modfunc = isset($_GET['func']) ? $_GET['func'] : 'run';

$page = new Controller();
$content = $page->mod_exec($modname, $modfunc, false);
echo $content;

printf("\n<!-- Page Generation: %0.22f -->", microtime(true) - $render_start);
?>