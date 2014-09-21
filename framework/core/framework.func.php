<?php
if (!defined('ACCESS')) exit('Access Denied!');
/**
 * 取得微秒+时间的值
 * @return number
 */
function get_micro_time(){
	list($a,$b) = explode(' ',microtime()); //获取并分割当前时间戳和微妙数，赋值给变量
	return $a+$b;
}

/**
 * 设置或者读取配置文件,框架的配置优先级高于APP的配置
 * 建议APP的配置文件的key加上APP的名称，防止冲突
 * @param unknown_type $key
 * @param unknown_type $value
 * @param unknown_type $force
 */
function config($key,$value=null,$force = false){
	global $globals_config;
	if(is_null($key))$globals_config;
	if(null==$value){return $globals_config[$key];}
	if(null!=$value){
		if(key_exists($key, $globals_config)&&!$force){
			return;//直接返回不覆盖
		}
		$globals_config[$key] = $value;
	}
}

/**
 * 实例化model模型
 * @param unknown_type $model_name
 */
function model($model_name=''){
	if(''==$model_name){
		return new  model();
	}
	
	$app_name = '';
	if(false!==strpos('.', $model_name)){
		list($app_name,$model_name) = explode('.', $model_name,2);
		//TODO,引入namespace解决不同app之间有model同名的问题
	}
	if(empty($app_name)){
		$app_name = $_GET['app'];
	}
	$model_name = $model_name.config('model_suffix');
	return framework::instance($model_name);
}

function url($action=null,$controller=null,$app=null,$params = array()){
	if(empty($action)) $action = application::get_action_name();
	if(empty($controller)) $controller = application::get_controller_name();
	if(empty($app)) $app = application::get_app_name();
		
	$route_type = config('url_type');
	switch ($route_type){
		//path方式
		//生成类似：eg:http://127.0.0.1/shf/user/user/add/name/zxy/age/23/sex/1
		case 1:
			$middle_str = $app.'/'.$controller.'/'.$action.'/';
			$paramsStr = '';
			if ($params) {
				foreach ($params as $k => $v) {
					$paramsStr .= $k . '/' . $v . '/';
				}
				$paramsStr = rtrim($paramsStr,'/');
			}
			return config('base_url') . $middle_str . $paramsStr;
			break;
			//生成rewrite方式url
			//生成类似：http://127.0.0.1/shf/index.php/user/user/add/?uid=100或者http://127.0.0.1/shf/user/user/add/?uid=100
		case 2:
			 
			break;
			//生成html方式url
			//生成类似：http://127.0.0.1/shf/index.php/user-add.htm?uid=100
		case 3:
			 
			break;
			//兼容模式
			//生成类似：http://<serverName>/appName/?s=/module/action/id/1/
		case 4:
			 
			break;
			//常规模式
		default:

	}
}

/**
 * 页面跳转，修改自Thinkphp
 * @param unknown_type $url
 * @param unknown_type $time
 * @param unknown_type $msg
 */
function redirect($url, $time=0, $msg='') {
	//多行URL地址支持
	$url        = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg))
		$msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		exit($str);
	}
}

// /**
//  * 设置或获取cookie
//  * @param unknown_type $key
//  * @param unknown_type $value $independent，是否是app独立的，true是，存储时候带上$_GET['app']前缀
//  * @param unknown_type $expire
//  * @param unknown_type $independent 是否是全局的，true全局，false和app有关的cookie
//  * @return unknown|Ambigous <string, unknown>
//  */
// //TODO,域名支持
// function cookie($key,$value=NULL,$expire=NULL,$gloabl=false){
// 	if (empty($key)) return $_COOKIE;
// 	if(!$gloabl) {
// 		$prefix = (empty($_GET['app']) ? '':$_GET['app']).'_';
// 	}else {
// 		$prefix = '';
// 	}
// 	$value_key = $prefix.$key;
// 	if(!is_null($key)&&empty($value)) return (isset($_COOKIE[$value_key])) ? $_COOKIE[$value_key] : '';
// 	$path_key = empty($prefix)? 'cookie_path':$prefix.'cookie_path';
// 	$path  = config($path_key);
// 	if(!empty($key)&&''==$value) {
// 		setcookie($key, '', time() - 3600, $path);
// 		unset($_COOKIE[$value_key]);
// 		return; 
// 	}
// 	$expire_key = empty($prefix)? 'cookie_expire':$prefix.'cookie_expire';
// 	$expire = ($expire==NULL)? config($expire_key):$expire;
// 	setcookie($key,$value,$expire,$path);
// 	$_COOKIE[$key]=$value;
// }

// /**
//  * session存取删除操作
//  * TODO,支持域名，支持分布式,过期时间
//  * @param unknown_type $key
//  * @param unknown_type $value
//  * @param unknown_type $gloabl
//  * @return unknown
//  */
// function session($key,$value=NULL,$gloabl=false){
// 	if (empty($key)) return $_SESSION;
// 	if(!$gloabl) {
// 		$prefix = (empty($_GET['app']) ? '':$_GET['app']).'_';
// 	}else {
// 		$prefix = '';
// 	}
// 	if (!session_id()) session_start();
// 	$value_key = $prefix.$key;
// 	if(!is_null($key)&&empty($value)) { return (isset($_SESSION[$value_key])) ? $_SESSION[$value_key] : NULL;}//返回$key对应的sesson
// 	if(!is_null($key)&&''==$value)    { unset($_SESSION[$value_key]);return;}//删除$key对应的session
// 	if(is_null($key)&&$value=='')     { session_destroy(); $_SESSION = array();retrun;}//清空session
// 	if(!is_null($key)&&!empty($value)){ $_SESSION[$value_key] = $value;return;}//设置session
// }

//截断
function mc_cut_str($sourcestr,$cutlength) {
	$returnstr='';
	$i=0;
	$n=0;
	$str_length=strlen($sourcestr);//字符串的字节数
	while (($n<$cutlength) and ($i<=$str_length)) {
		$temp_str=substr($sourcestr,$i,1);
		$ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
		if ($ascnum>=224)    //如果ASCII位高与224，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
			$i=$i+3;            //实际Byte计为3
			$n++;            //字串长度计1
		}
		elseif ($ascnum>=192) //如果ASCII位高与192，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
			$i=$i+2;            //实际Byte计为2
			$n++;            //字串长度计1
		}
		elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1;            //实际的Byte数仍计1个
			$n++;            //但考虑整体美观，大写字母计成一个高位字符
		}
		else                //其他情况下，包括小写字母和半角标点符号，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1;            //实际的Byte数计1个
			$n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽…
		}
	}
	if ($str_length>$cutlength){
		$returnstr = $returnstr . '…';//超过长度时在尾处加上省略号
	}
	return $returnstr;
}

//HTML危险标签过滤
function mc_remove_html($str) {
	$str = htmlspecialchars_decode($str);
	$str=preg_replace("/\s+/", " ", $str); //过滤多余回车
	$str=preg_replace("/<[ ]+/si","<",$str); //过滤<__("<"号后面带空格)

	$str=preg_replace("/<\!--.*?-->/si","",$str); //注释
	$str=preg_replace("/<(\!.*?)>/si","",$str); //过滤DOCTYPE
	$str=preg_replace("/<(\/?html.*?)>/si","",$str); //过滤html标签
	$str=preg_replace("/<(\/?head.*?)>/si","",$str); //过滤head标签
	$str=preg_replace("/<(\/?meta.*?)>/si","",$str); //过滤meta标签
	$str=preg_replace("/<(\/?body.*?)>/si","",$str); //过滤body标签
	$str=preg_replace("/<(\/?link.*?)>/si","",$str); //过滤link标签
	$str=preg_replace("/<(\/?form.*?)>/si","",$str); //过滤form标签
	$str=preg_replace("/cookie/si","COOKIE",$str); //过滤COOKIE标签
	
	$str=preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si","",$str); //过滤applet标签
	$str=preg_replace("/<(\/?applet.*?)>/si","",$str); //过滤applet标签

	$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
	$str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签

	$str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si","",$str); //过滤title标签
	$str=preg_replace("/<(\/?title.*?)>/si","",$str); //过滤title标签

	$str=preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si","",$str); //过滤object标签
	$str=preg_replace("/<(\/?objec.*?)>/si","",$str); //过滤object标签

	$str=preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si","",$str); //过滤noframes标签
	$str=preg_replace("/<(\/?noframes.*?)>/si","",$str); //过滤noframes标签

	$str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); //过滤frame标签
	$str=preg_replace("/<(\/?i?frame.*?)>/si","",$str); //过滤frame标签

	$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
	$str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
	$str=preg_replace("/javascript/si","Javascript",$str); //过滤script标签
	$str=preg_replace("/vbscript/si","Vbscript",$str); //过滤script标签
	$str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str); //过滤script标签
	$str=preg_replace("/&#/si","&＃",$str); //过滤script标签
	return $str;
}

/**
 * 创建目录
 */
function makeDir($dirName) {
	if (!$dirName) {
		return false;
	}
	
	if (!is_dir($dirName)) {
		mkdir($dirName, 0777, true);
	} else if (!is_writable($dirName)) {
		//更改目录权限
		chmod($dirName, 0777);
	}
}

/**
 * 获取客户端ip
 * @return string
 */
function getIp() {
	// Gets the default ip sent by the user
	if (!empty($_SERVER['REMOTE_ADDR'])) {
		$step = 1;
		$direct_ip = $_SERVER['REMOTE_ADDR'];
	}
	// Gets the proxy ip sent by the user
	$proxy_ip     = '';
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$step = 2;
		$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
		$step = 3;
		$proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
	} else if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
		$step = 4;
		$proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
	} else if (!empty($_SERVER['HTTP_FORWARDED'])) {
		$step = 5;
		$proxy_ip = $_SERVER['HTTP_FORWARDED'];
	} else if (!empty($_SERVER['HTTP_VIA'])) {
		$step = 6;
		$proxy_ip = $_SERVER['HTTP_VIA'];
	} else if (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
		$step = 7;
		$proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
	} else if (!empty($_SERVER['HTTP_COMING_FROM'])) {
		$step = 8;
		$proxy_ip = $_SERVER['HTTP_COMING_FROM'];
	}
	$ip = ($proxy_ip == '')? $direct_ip:$proxy_ip;
	return $ip;
}


/**
 * 随机数
 * @return string
 */
function generateRand($length=3)
{
	$string = '';
	$uppercase  = range('A', 'Z');
	$lowercase  = range('a', 'z');
	$numeric    = range(0, 9);

	$CharPool   = array_merge($uppercase, $numeric,$lowercase);
	$PoolLength = count($CharPool) - 1;

	for ($i = 0; $i < $length; $i++)
	{
	$string .= $CharPool[mt_rand(0, $PoolLength)];
	}

	return $string;
}

/**
 * 抛出异常
 * @param unknown_type $msg
 * @param unknown_type $code
 * @throws  Exception
 */
function exception($msg, $code=0) {
	throw new Exception($msg, $code);
}

/**
 * 引用自Thinkphp
 * TODO，加入异常统计机制
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m
 * @return mixed
 */
function page_statistic($start,$end='',$dec=4) {
	static $_info       =   array();
	static $_mem        =   array();
	if(is_float($end)) { // 记录时间
		$_info[$start]  =   $end;
	}elseif(!empty($end)){ // 统计时间和内存使用
		if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
		if(MEMORY_LIMIT_ON && $dec=='m'){
			if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
			return number_format(($_mem[$end]-$_mem[$start])/1024);
		}else{
			return number_format(($_info[$end]-$_info[$start]),$dec);
		}
	}else{ // 记录时间和内存使用
		$_info[$start]  =  microtime(TRUE);
		if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
	}
}

/**
 * 引用自Thinkphp
 * TODO,增加监测SQL异常的机制
 * 设置和获取统计数据
 * 使用方法:
 * <code>
 * N('db',1); // 记录数据库操作次数
 * N('read',1); // 记录读取次数
 * echo N('db'); // 获取当前页面数据库的所有操作次数
 * echo N('read'); // 获取当前页面读取次数
 * </code>
 * @param string $key 标识位置
 * @param integer $step 步进值
 * @return mixed
 */
function sql_statistic($key, $step=0) {
	static $_num    = array();
	if (!isset($_num[$key])) {
		$_num[$key] = 0;
	}
	if (empty($step))
		return $_num[$key];
	else
		$_num[$key] = $_num[$key] + (int) $step;
}

