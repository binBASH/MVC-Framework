<?php
interface DB_Interface
{
	public static function get_connection($driver, $db_host, $db_username, $db_password, $db_name);
	
	public function query($query);
	
	public function update($query);
	
	public function prepare($query);
	
	public function escape($val);
	
	public function get_insert_id();
	
	public function get_rows($res);
	
	public function get_row($res);
	
	public function close();
}

abstract class DB implements DB_Interface 
{
	public static function get_connection($driver, $db_host, $db_username, $db_password, $db_name) {
	   $driver = 'db_' . $driver;
	   return new $driver($db_host, $db_username, $db_password, $db_name);
	}
}
?>