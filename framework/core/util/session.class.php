// <?php
if (!defined('ACCESS')) exit('Access Denied!');
class session {
	
	/**
 	 * Session-设置session值
 	 * 使用方法：$this->getUtil('session')->set('ccccccc', 'dddddd');
	 * @param  string $key    key值，可以为单个key值，也可以为数组
	 * @param  string $value  value值
 	 * @return string   
	 */
	public static function set($key, $value='') {
		if (!session_id()) self::start();
		if (!is_array($key)) {
			$_SESSION[$key] = $value;
		} else {
			foreach ($key as $k => $v) $_SESSION[$k] = $v;
		}
		return true;
	}
	
	/**
 	 * Session-获取session值
 	 * 使用方法：$this->getUtil('session')->get('ccccccc');
	 * @param  string $key    key值
 	 * @return string   
	 */
	public static function get($key) {
		if (!session_id()) self::start();
		return (isset($_SESSION[$key])) ? $_SESSION[$key] : NULL;
	}
	
	/**
 	 * Session-删除session值
 	 * 使用方法：$this->getUtil('session')->del('ccccccc');
	 * @param  string $key    key值
 	 * @return string   
	 */
	public static function del($key) {
		if (!session_id())  self::start();
		if (is_array($key)) {
			foreach ($key as $k){
				if (isset($_SESSION[$k])) unset($_SESSION[$k]);
			}
		} else {
			if (isset($_SESSION[$key])) unset($_SESSION[$key]);
		}
		return true;
	}
	
	/**
 	 * Session-清空session
  	 * 使用方法：$this->getUtil('session')->clear();
 	 * @return   
	 */
	public static function clear() {
		if (!session_id())  self::start();
		session_destroy();
		$_SESSION = array();
	}
	
	/**
 	 * Session-session_start()
 	 * @return string   
	 */
	private static function start() {
		session_start();
	}
	
}
