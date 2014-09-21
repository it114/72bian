<?php
if (!defined('ACCESS')) exit('Access Denied!');
class validate {

	/**
	 * 安全过滤类-加反斜杠，放置SQL注入
	 *  Controller中使用方法：$this->controller->filter_slashes(&$value)
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public static function filter_slashes(&$value) {
		if (get_magic_quotes_gpc()) return false; //开启魔术变量
		$value = (array) $value;
		foreach ($value as $key => $val) {
			if (is_array($val)) {
				self::filter_slashes($value[$key]);
			} else {
				$value[$key] = addslashes($val);
			}
		}
	}
	
	/**
	 * 过滤不安全字符串
	 */
	public static function filter() {
		if (is_array($_SERVER)) {
			foreach ($_SERVER as $k => $v) {
				if (isset($_SERVER[$k])) {
					$_SERVER[$k] = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '', $v);
				}
			}
		}
		unset($_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);//不使用老的Api
		self::filter_slashes($_GET);
		self::filter_slashes($_POST);
		self::filter_slashes($_COOKIE);
		self::filter_slashes($_FILES);
		self::filter_slashes($_REQUEST);
	}
	
	/**
	 *	数据基础验证-是否是QQ
	 *  Controller中使用方法：$this->controller->is_qq($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public static function is_qq($value) {
		return preg_match('/^[1-9]\d{4,12}$/', trim($value));
	}
	
	public static function is_url($value) {
		return preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是身份证
	 *  Controller中使用方法：$this->controller->is_card($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public static function is_card($value){
		return preg_match("/^(\d{15}|\d{17}[\dx])$/i", $value);
	}
	
	/**
	 *	数据基础验证-是否是数字类型
	 *  Controller中使用方法：$this->controller->is_number($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public static function is_number($value) {
		return preg_match('/^\d{0,}$/', trim($value));
	}
	
	public static function is_email($value) {
		return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是IP
	 *  Controller中使用方法：$this->controller->is_ip($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public static function is_ip($value) {
		return preg_match('/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/', trim($value));
	}
	
	public static function is_empty($value) {
		if ($value===NULL||empty($value) || $value=="") return true;
		return false;
	}
	
	/**
	 *	数据基础验证-检测数组，数组为空时候也返回FALSH
	 *  Controller中使用方法：$this->controller->is_arr($value)
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public static function is_arr($value) {
		if (!is_array($value) || empty($value)) return false;
		return true;
	}
	 
}

