 <?php
/**
 *
 * @author zxy
 *
 */
define('ACCESS',1);
// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();


/**
 * 时区设置
 */
date_default_timezone_set('PRC');

if(!defined('ACCESS')) {
	exit('Access Denied');
}

if(!defined('DEBUG')){
	define('DEBUG', 1);
}

if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}

if(!defined('ROOT_PATH')){
	define('ROOT_PATH', dirname(dirname(__FILE__)).DS);
}

if(!defined('APP_PATH')){
	define('APP_PATH', dirname(dirname(__FILE__)).DS.'apps'.DS);
}

if(!defined('FRAMEWORK_PATH')){
	define('FRAMEWORK_PATH', dirname(dirname(__FILE__)).DS.'framework'.DS);
}

if(!function_exists('self_auto_load')){
	function self_auto_load($class=''){
		//TODO,第三方自定义加载类规则
		return false;
	}
}

require FRAMEWORK_PATH.'global_config.inc.php';
require FRAMEWORK_PATH.'core'.DS.'framework.func.php';
$t1 = get_micro_time();
//require framework
require(FRAMEWORK_PATH.DS.'core'.DS.'framework.class.php');
//app common function 
$comm_func_file = APP_PATH.'common.func.php';
if(is_file($comm_func_file)) require $comm_func_file;
//类库加载规则
spl_autoload_register('framework::auto_load');
//filer
//关闭魔术变量，提高PHP运行效率.
if (get_magic_quotes_runtime()) {
	@set_magic_quotes_runtime(0);
}
//过滤请求参数
validate::filter(); 
//route
// $route = new route();
// $route->start();
framework::instance('route')->start();
framework::init();
//loads controller
$application = new application();
framework::get_log_handler()->record("test");
framework::get_log_handler()->save();
//调试模式。显示统计信息
if(DEBUG){
// 	echo '<hr/>';
// 	echo '<div style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;">';
// // 	page_statistic('beginTime',$GLOBALS['_beginTime']);
// // 	page_statistic('viewEndTime');
// 	$t2 = get_micro_time();
// 	echo '<pre>';
// 	echo  '运行时间：'.($t2-$t1).'s,内存使用:'.(MEMORY_LIMIT_ON?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb':'不支持').'<br/>';
// 	echo '执行SQL操作次数:'.sql_statistic('db').',读次数：'.sql_statistic('read').',写次数：'.sql_statistic('write').'<br/>';
// 	echo '加载文件'.count(get_included_files()).'个'.'<br/>';
// 	$info = array();
// 	$files = get_included_files();
// 	foreach ($files as $key=>$file){
// 		echo $file.' ( '.number_format(filesize($file)/1024,2).' KB )'.'<br/>';
// 	}
// 	echo '</pre>';
// 	echo '</div>';
}

