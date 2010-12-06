<?php
class Application
{
	private $global_data = array();
	private $data = array();

	private $no_general_template = false;
	private $no_template = false;

	public function set_no_general_template() {
		$this->no_general_template = true;
	}

	public function get_no_general_template() {
		return $this->no_general_template;
	}

	public function set_no_template() {
		$this->no_template = true;
	}

	public function get_no_template() {
		return $this->no_template;
	}

	/**
	 * Set template variable global
	 */
	public function set_global_var($k, $v) {
		$this->global_data[$k] = $v;
	}

	/**
	 * Get template variable global
	 */
	public function get_global_var($k) {
		return $this->global_data[$k];
	}

	public function get_global_vars() {
		return $this->global_data;
	}

	/**
	 * Set template variable
	 */
	public function set_var($k, $v) {
		$this->data[$k] = $v;
	}

	/**
	 * Get template variable
	 */
	public function get_var($k) {
		return $this->data[$k];
	}

	public function get_vars() {
		return $this->data;
	}

	public function plug($modname, $modfunc) {
		$plugin = new Controller();
		return $plugin->mod_exec($modname, $modfunc, true);
	}
}
?>