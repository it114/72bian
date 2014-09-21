<?php

class framework{
	
	protected static $class_cotainer = array('instance_class'=>array()); //单例
	/**
	 * class_name & true_path maping .
	 * @var unknown_type
	 */
	static $class_mapping = array(
			//cache dir
			'cacheFileHandler'	=>		'framework/core/cache/cacheFileHandler.class.php',
			//db dir
			'medoo'				=>		'framework/core/db/medoo.class.php',
			//util dir
			'request'			=>		'framework/core/util/request.class.php',
			'response'  		=>		'framework/core/util/response.class.php',
			'route'     		=>      'framework/core/util/route.class.php',
			'validate'  		=>		'framework/core/util/validate.class.php',
			'cookie'			=>		'framework/core/util/cookie.class.php',
			'session'			=>		'framework/core/util/session.class.php',
			//log dir
			'logFileHandler'    =>		'framework/core/log/logFileHandler.class.php',
			//view dir
			'view'				=>		'framework/core/view/view.class.php',	
	);
	
	/**
	 * 初始化，扫描根目录下的指定的文件夹中的指定后缀的文件，生成mapper文件,这里先写死
	 */
	public static function init(){
		// 设定错误和异常处理
// 		register_shutdown_function('framework::fatalError');
// 		set_error_handler('framework::appError');
// 		set_exception_handler('framework::appException');
		
		//TODO,扫描文件夹生成文件
		//TODO,加载app配置文件
		
		//加载app下面文件
		$app_common_file = APP_PATH.$_GET['app'].DS.'common.func.php';
		if(is_file($app_common_file)){
			include $app_common_file;
		}
		$app_config_file = APP_PATH.$_GET['app'].DS.'appconfig.inc.php';  
		if(is_file($app_config_file)){
			$config = include $app_config_file;
			global $globals_config;
			$globals_config = array_merge($globals_config);//可以快捷访问$value = config($key);
		}
	}
	
	/**
	 * 异常提示输出&程序终止   修改自Thinkphp
	 * @param unknown_type $error
	 */
	public static function halt($error) {
		$e = array();
		if (DEBUG) {
			//调试模式下输出错误信息
			if (!is_array($error)) {
				$trace          = debug_backtrace();
				$e['message']   = $error;
				$e['file']      = $trace[0]['file'];
				$e['line']      = $trace[0]['line'];
				ob_start();
				debug_print_backtrace();
				$e['trace']     = ob_get_clean();
			} else {
				$e              = $error;
			}
		} else {
			//否则定向到错误页面
			$error_page         = config('error_page');
			if (!empty($error_page)) {
				redirect($error_page);
			} else {
				$message        = is_array($error) ? $error['message'] : $error;
				$e['message']   = config('error_msg')?  config('error_msg'):$message;
			}
		}
		// 包含异常页面模板
		$exceptionFile =  FRAMEWORK_PATH.'res/view/exception.tpl';
		include $exceptionFile;
		exit;
	}
	
	/**
	 *  致命异常处理函数，修改自Thinkphp
	 */
	public static function fatalError() {
		framework::get_log_handler()->save();
		if ($e = error_get_last()) {
			switch($e['type']){
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					ob_end_clean();
					self::halt($e);
					break;
			}
		}
	}
	/**
	 * set_error_handler 注册的错误处理器使用的函数， 修改自Thinkphp
	 * @param unknown_type $errno
	 * @param unknown_type $errstr
	 * @param unknown_type $errfile
	 * @param unknown_type $errline
	 */
	public static function appError($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				ob_end_clean();
				$errorStr = "$errstr ".$errfile." 第 $errline 行.";
				framework::get_log_handler()->write("[$errno] ".$errorStr,'error');
				self::halt($errorStr);
				break;
			default:
				$errorStr = "[$errno] $errstr ".$errfile." 第 $errline 行.";
				self::trace($errorStr,'','info');
				break;
		}
	}
	
	public static function trace($value='',$label='',$level='debug',$record=false) {
		static $_trace =  array();
		$info   =   ($label?$label.':':'').print_r($value,true);
		if(!isset($_trace[$level]) || count($_trace[$level])>50) {
			$_trace[$level] =   array();
		}
		$_trace[$level][]   =   $info;
	}
	
	//添加自己的类、路径映射
	public static  function add_class_mapping($class, $path=''){
		if(is_array($class)){
			self::$class_mapping = array_merge(self::$class_mapping, $class);
		}else{
			self::$class_mapping[$class] = $path;
		}
	}
	
	/*
	
	* 支持子目录
	* @param string $class
	* @return boolean
	*/
	public static function auto_load($class){
		if(key_exists($class, self::$class_mapping)){
			framework::import_cache($class,ROOT_PATH.self::$class_mapping[$class]);
			return;
		}
		//auto load app model
		if(substr($class, -5)=='Model') { 
			framework::import_cache($class, ROOT_PATH.'apps'.DS.$_GET['app'].DS.'models'.DS.$class.'.class.php');
			return;
		}
		//auto load app controller 
		if(substr($class, -10)=='Controller') { 
			framework::import_cache($class,ROOT_PATH.'apps'.DS.$_GET['app'].DS.'controllers'.DS.$class.'.class.php');
			return;
		}
		//auto load app lib 
		if(substr($class, -4)=='Alib') {
			framework::import_cache($class, ROOT_PATH.'apps'.DS.$_GET['app'].DS.'lib'.DS.$class.'.class.php');
			return;
		}
		//auto load framework 中共享的 lib
		if(substr($class, -4)=='Flib') {
			framework::import_cache($class, FRAMEWORK_PATH.'lib'.DS.$class.'.class.php');
			return;
		}
		
		//第三方自动加载扩展，需要在框架init的时候定义self_auto_load函数
		if(function_exists('self_auto_load')){
			return call_user_func('self_auto_load',$class);
		}
		
		throw new Exception(" class  ".$class." not exists ");
	}
	
	/**
	 * import ，返回true代表从缓存加载
	 * @param unknown_type $class_name
	 * @param unknown_type $true_path
	 */
	public static function import_cache($class_name,$true_path){ 
		if (!isset(self::$class_cotainer['imported_file'][$class_name])) {
			if (!file_exists($true_path)) die('File :'.$true_path.' not exists.');
			require_once($true_path);
			return false;
		}
		return true;
	}
	
	/**
	 *  导入并实例化,并以class_name为key保存下次不再重复加载
	 *  当class_name中有$delimiter代表加载绝对路径的class不走自动加载的路径，否则自动加载class
	 * 【重要】：php不允许加载两个同名的class，所以如果两个不同的目录有同名的class，会返回已经加载的class
	 * //TODO,测试str_replace的效率问题
	 * @param unknown_type $class
	 * @param unknown_type $delimiter
	 * @param unknown_type $ext
	 */
	public static function instance($class_name,$delimiter='.',$ext='.class.php') {
		if (!isset(self::$class_cotainer['instance_class'][$class_name])) {
			if(false!==strpos($delimiter, $class_name)){
				framework::import($class_name,$delimiter,$ext);
			}
			$loaded_class = new $class_name;
			self::$class_cotainer['instance_class'][$class_name] = $loaded_class; 
		} 
		return self::$class_cotainer['instance_class'][$class_name];
	}
	
	/**
	 * 导入类，成功返回导入类的文件名
	 * @param unknown_type $class
	 * @param unknown_type $delimiter
	 * @param unknown_type $ext
	 */
	public static function import($class,$delimiter='.',$ext='.class.php'){
		if(strpos(!$delimiter, $class)) die('Error class :'.$class.' missing delimiter:'.$delimiter);//不导入ROOT_PATH下面的类
		$true_path = str_replace($delimiter,DS,strtolower($class));
		$class_name = explode(DS, $true_path);
		$class_name = $class_name[count($class_name)-1];
		$true_path = ROOT_PATH.$true_path.$ext;
		framework::import_cache($class_name, $true_path);
		return $class_name;
	}
	
	/**
	 * 获取日志记录处理器句柄
	 * @param unknown_type $handler
	 */
	public static function get_log_handler($handler=''){
		$handler = config('log_handler');//日志记录类型，TODO,日志记录类型支持多样化，目前只支持本地文件方式
		if(!$handler||$handler=='file'){
			$handler = framework::instance('logFileHandler');
		}
		return $handler;
	}
	
	/**
	 * 获取缓存处理器句柄
	 * @param unknown_type $handler
	 * @return Ambigous <cacheFileHandler, void>
	 */
	public static function get_cache_hadler($handler=''){
		$handler = config('cache_handler');//缓存处理器类型，TODO,日志记录类型支持多样化，目前只支持本地文件方式
		if(!$handler) {
			$handler = cacheFileHandler::getInstance();
		}
		return $handler;
	}
	
	
}

/**
 *
 * @author zxy
 *
 */
class application 
{
	function __construct()
	{
		$this->run_controller();
	}
	
	public static function get_app_name(){
		return $_GET['app'];
	}
	
	public static function get_controller_name() {
        return $_GET['controller'];
    } 
    
    public static function get_action_name(){
    	return  $_GET['action'];
    }
    
	public function run_controller(){
		$controller_file_name = $_GET['controller'].config('controller_suffix');
		$controller = new $controller_file_name();
		if($controller){
			$action = $_GET['action'].config('action_suffix');
			if( method_exists( $controller, $action)){
				$this->before_controller($controller, config('action_suffix'));
				$controller->{$action}();
				$this->after_controller($controller, config('action_suffix'));
			} else{
				//display default html page
				$controller_method_view_name = ROOT_PATH.'apps'.DS.$_GET['app'].DS.'views'.DS.$_GET['controller'].DS.$_GET['action'].'.html';
				if(is_file($controller_method_view_name)){
					include  $controller_method_view_name;
				} else { 
					response::response_404();
				}
			}
		} else { 
			response::response_404();
		}
	}
	
	private function before_controller($controller,$def_action_suffix){
		$action = '_before'.$def_action_suffix;  
		if(method_exists($controller, $action)){
			$controller->{$action}();
		}
	}
	
	private function after_controller($controller,$def_action_suffix){
		$action = '_after'.$def_action_suffix; 
		if(method_exists($controller, $action)){
			$controller->{$action}();
		}
	}
}

class controller extends application
{
	/**
	 * 视图实例化变量
	 *
	 * @var object
	 */
	protected static $_view;
	
	function __construct() {
		//加载视图类文件，目前只支持PHP原生语法的模板
		self::$_view   =  new view();
		$this->assign('__base_url__',config('base_url'));
	}
	
	/**********************快捷方法end******************************/
	
	
	/**********************View相关方法start******************************/
	public function setTheme($themeName = 'default') {
		return self::$_view->setTheme($themeName);
	}
	
	/**
	 * 设置视图文件布局结构的文件名(layout)
	 *
	 * layout默认为:空(null)
	 * @access public
	 * @param string $layoutName    所要设置的layout名称
	 * @return string                layout名称
	 */
	public function setLayout($layoutName = null) {
		return self::$_view->setLayout($layoutName);
	}
	
	/**
	 * 分析视图缓存,如果存在缓存文件，则直接返回，不用查询数据库等操作了
	 *
	 * @access public
	 * @param string $cacheId 缓存ID
	 * @param integer $lifetime 缓存周期
	 * @return void
	 */
	public function cache($cacheId = null, $lifetime = null) {
		return self::$_view->cache($cacheId, $lifetime);
	}
	
	/**
	 * 视图变量赋值操作
	 *
	 * @access public
	 * @param mixted $keys 视图变量名
	 * @param string $value 视图变量值
	 * @return mixted
	 */
	public function assign($keys, $value = null) {
		return self::$_view->assign($keys, $value);
	}
	
	/**
	 * 显示当前页面的视图内容
	 *
	 * 包括视图页面中所含有的挂件(widget), 视图布局结构(layout), 及render()所加载的视图片段等
	 * @access public
	 * @param string $fileName 视图名称
	 * @return void
	 */
	public function display($fileName = null) {
		return self::$_view->display($fileName);
	}
	
	/**
	 * 加载并显示视图片段文件内容
	 *
	 * 相当于include 代码片段，当$return为:true时返回代码代码片段内容,反之则显示代码片段内容
	 * @access public
	 * @param string  $fileName 视图片段文件名称
	 * @param array   $_data     视图模板变量，注：数组型
	 * @param boolean $return    视图内容是否为返回，当为true时为返回，为false时则为显示. 默认为:false
	 * @return mixed
	 */
	public function render($fileName, $_data = array(), $return = false) {
		return self::$_view->render($fileName, $_data, $return);
	}
	/**********************View相关方法end******************************/
}


class model
{
	protected $table_name;
	protected $model_name;
	protected static $db_container = array();
	public function __construct(){
	}
	
	public static function __instance(){ 
		$database=0;
		if(@self::$db_container[$database]  == null ){
			framework::import('framework.core.db.medoo'); 
			self::$db_container[$database] = new medoo($database );
			return self::$db_container[$database];
		}
		return self::$db_container[$database];
	}
	
	protected function get_table_name($_table_name){
		if(!empty($this->table_name)){
			return $this->table_name;
		}
		return (config('db_table_prefix').$_table_name);
	}
	
	public  function last_query($database=0) {
		return self::__instance($database)->last_query();
	}
}


// class base {
// 	private static $_instance;
// 	protected function __construct(){
// 	}
// 	protected static function getInstance($class_name){
// 		if(!self::$_instance){
// 			self::$_instance = new $class_name();
// 		}
// 		return self::$_instance;
// 	}
	
	 
// }

