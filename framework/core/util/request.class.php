<?php
 
if (!defined('ACCESS')) exit('Access Denied!');
class request {
	
	public static function get($var_name=''){
		if (empty($var_name)) return $_GET;
		return (isset($_GET[$var_name])) ? $_GET[$var_name] : '';
	}
	
	public static function post($var_name=''){
		if (empty($var_name)) return $_POST;
		return (isset($_POST[$var_name])) ? $_POST[$var_name] : '';
	}
	
	public static function session($var_name=''){
		if (empty($var_name)) return $_SESSION;
		return (isset($_SESSION[$var_name])) ? $_SESSION[$var_name] : '';
	}
	
	public static function cookie($var_name=''){
		if (empty($var_name)) return $_COOKIE;
		return (isset($_COOKIE[$var_name])) ? $_COOKIE[$var_name] : '';
	}
	
	public static function env($var_name=''){
		if (empty($var_name)) return $_ENV;
		return (isset($_ENV[$var_name])) ? $_ENV[$var_name] : '';
	}
	
	public static function server($var_name = '') {
		if (empty($var_name)) return $_SERVER;
		return (isset($_SERVER[$var_name])) ? $_SERVER[$var_name] : '';
	}
	
	/**
	 * 1、根据HTTP_X_REQUESTED_WITH来判断
	 * 2、根据请求中自定义的is_ajax来判断
	 */
	public static function is_ajax() {
		if ($this->server('HTTP_X_REQUESTED_WITH') && strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') return true;
		if ($this->post('is_ajax') || $this->get('is_ajax')) return true;  
		return false;
	}
	
	public static function is_post() {
		return (strtolower($this->server('REQUEST_METHOD')) == 'post') ? true : false;
	}
	
	public static function is_get() {
		return (strtolower($this->server('REQUEST_METHOD')) == 'get') ? true : false;
	}
	
	
}

