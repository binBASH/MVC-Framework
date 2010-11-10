<?php
class Debug
{
	private static $debugmode = 'silent';

	public static function set_mode($debugmode) {
		self::$debugmode = $debugmode;
	}

	public static function out($msg, $mode = null) {
		if ( $mode == 'echo' ) {
			print_r($msg);
		} elseif ( $mode == 'log') {
			error_log(print_r($msg, true));
		} elseif ( self::$debugmode == 'echo' ) {
			print_r($msg);
		} elseif ( self::$debugmode == 'log' ) {
			error_log(print_r($msg, true));
		}
	}

	private function __construct() {}

	private function __clone() {}
}
?>