<?php
class TPL
{
    private $TemplateDirectory;
    private $CompileDirectory;
    private $data;
	private $allowed_methods;
	
	private $tplname;
	private $subdir;
	
    public function __construct($tplname, $subdir='') {
        $this->tplname = $tplname;
        $this->subdir = $subdir;
		$this->allowed_methods = array();
        $this->clear();
        $this->TemplateDirectory = '../templates';
        $this->CompileDirectory = $this->TemplateDirectory . '/_compiled';
    }

	public function allow_method($method) {
		$this->allowed_methods[] = $method;
	}

    public function clear() {
		$this->data = new stdClass;
    }

    public function set($key, $value) {
		if(is_array($key)) {
			foreach($key as $n=>$v)
				$this->data->$n = $v;
		} elseif(is_object($key)) {
			foreach(get_object_vars($key) as $n=>$v)
				$this->data->$n = $v;
		} else {
			$this->data->$key = $value;
		}
    }
    
    public function set_data($data) {
    	$this->data = $data;
    }
    
    public function get_data() {
    	return $this->data;
    }
    
    private function compile() {
        $dir_additional = $this->subdir != '' ? $this->subdir . '/' . $this->tplname : $this->tplname;    
        $tplfile = $this->TemplateDirectory . '/' . $dir_additional;
        if( !file_exists($tplfile) )
            throw new Exception("Template does not exist (" . $tplfile . ')');

		if( !file_exists($this->CompileDirectory) )
			mkdir($this->CompileDirectory);

		if( $this->subdir != '' && !file_exists($this->CompileDirectory . '/' . $this->subdir) )
			mkdir($this->CompileDirectory . '/' . $this->subdir);			

		$compiledFile = $this->CompileDirectory . '/' . $dir_additional . '.php';

		// don't compile if nothing has changed		
		if( file_exists($compiledFile) && filemtime($compiledFile) >= filemtime($tplfile) )
			return;
		
		$tmp_lines = file($tplfile);
		foreach( $tmp_lines as $line ) {
			$num = preg_match_all('/\{\{include:([^{}]+)\}\}/i', $line, $matches);
			if( $num > 0 ) {
				for( $i = 0; $i < $num; $i++ ) {
					$inc_file = basename($matches[1][$i]);
        			$inc_dir_additional = $this->subdir != '' ? $this->subdir . '/' . $inc_file : $inc_file;
        			$inc_tplfile = $this->TemplateDirectory . '/' . $inc_dir_additional;
					if( file_exists($inc_tplfile) ) {
						$inc_lines = file($inc_tplfile);
						foreach( $inc_lines as $inc_line ) {
							$lines[] = $inc_line;
						}
					}
				}
			} else $lines[] = $line;
		}		
		
		$newLines = array();
		$matches = null;
		foreach( $lines as $line ) {
			$num = preg_match_all('/\{\{([^{}]+)\}\}/', $line, $matches);
			if( $num > 0 ) {
				for( $i = 0; $i < $num; $i++ ) {
					$match = $matches[0][$i];
					$new = $this->transform_syntax($matches[1][$i]);
					$line = str_replace($match, $new, $line);
				}
			}
			
			if( trim($line) != '<?php  ?>' )
				$newLines[] = $line;
		}
		$f = fopen($compiledFile, 'w');
		fwrite($f, implode('',$newLines));
		fclose($f);
    }

	private function transform_syntax($input) {
		$from = array(
			'/(^|\[|,|\(|\+| )([a-zA-Z_][a-zA-Z0-9_]*)($|\.|\)|\[|\]|\+)/',
			'/(^|\[|,|\(|\+| )([a-zA-Z_][a-zA-Z0-9_]*)($|\.|\)|\[|\]|\+)/', // again to catch those bypassed by overlapping start/end characters
			'/\./'
		);
		
		$to = array(
			'$1$this->data->$2$3',
			'$1$this->data->$2$3',
			'->'
		);

		$parts = explode(':', $input);
		$parts[0] = strtolower($parts[0]);
		
		$str = '<?php ';
		switch($parts[0]) { // check for a template statement
			case 'if':
			case 'switch':
				$str .= $parts[0] . '(' . preg_replace($from, $to, $parts[1]) . ') {' . ($parts[0] == 'switch' ? ' default:' : '');
				break;
			case 'case':
				$str .= 'break; case ' . preg_replace($from, $to, $parts[1]) . ':';
				break;
			case 'foreach':
				$pieces = explode(',', $parts[1]);
				$str .= 'foreach(' . preg_replace($from, $to, $pieces[0]) . ' as ';
				$str .= preg_replace($from, $to, $pieces[1]);
				if(sizeof($pieces) == 3) // prepares the $value portion of foreach($var as $key=>$value)
					$str .= '=>' . preg_replace($from, $to, $pieces[2]);
				$str .= ') {';
				break;
			case 'end':
				$str .= '}';
				break;
			case 'else':
				$str .= '} else {';
				break;
            case 'elseif':
                $str .= '} ' . $parts[0] . '(' . preg_replace($from, $to, $parts[1]) . ') {';
                break;
            case 'comment':
            	break;
			default:
				$num = preg_match_all('/^[0-9a-zA-Z]+\(/', $parts[0], $matches);
				if( $num > 0 )
					foreach( $matches as $match ) {
						$func = substr($match[0], 0, -1);
						if( !in_array($func, $this->allowed_methods) )
							return '';
					}
					
				$repl = preg_replace($from, $to, $parts[0]);
				if( trim($repl) != '' )
					$str .= 'echo ' . $repl . ';';
				break;
		}
		$str .= ' ?>';
		return $str;
	}

    public function display() {
		$this->compile();
        $dir_additional = $this->subdir != '' ? $this->subdir . '/' . $this->tplname : $this->tplname;
		include($this->CompileDirectory . '/' . $dir_additional . '.php');
    }
    
    public function inline() {
        ob_start();
        $this->display();
        $content = ob_get_clean();
        ob_end_flush();
        return $content;
    }
}
?>