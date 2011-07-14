<?php
class SimpleDarkOptions implements ArrayAccess {

	private static $instance;

	private $options_array = array();

	private function __construct() {
		if($o = get_option(SIMPLEDARK_OPTIONS)) {
			$this->options_array = $o;
			unset($o);
		}
	}
	
	public function refresh() {
		$this->__construct();
	}

	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new SimpleDarkOptions();
		}
		return self::$instance;
	}

	public function offsetExists($key) {
		return array_key_exists($key, $this->options_array);
	}

	public function offsetGet($key) {
		if(array_key_exists($key, $this->options_array)) {
			if(is_string($this->options_array[$key])) {
				return stripslashes($this->options_array[$key]);
			} else {
				return $this->options_array[$key];
			}
		} else {
			return null;
		}
	}

	public function offsetSet($key, $val) {
		$this->options_array[$key] = $val;
	}

	public function offsetUnset($key) {
		if(array_key_exists($key, $this->options_array)) {
			unset($this->options_array[$key]);
		}
	}

	public function merge_array($true_array, $override=false) {
		foreach($true_array as $key => $val) {
			if(!array_key_exists($key, $this->options_array) || $override) {
				$this->options_array[$key] = $val;
			}
		}
		return $this->options_array;
	}
}
?>