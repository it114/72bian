<?php
if (!defined('ACCESS')) exit('Access Denied!');
//Db Config
$DATABASE_LIST = array(
			//read db
			array (
					'server'		=>		'127.0.0.1',
					'port'			=>		'3306',
					'username'		=> 		'root',
					'password'		=>		'',
					'db_name'		=>	 	'72bian'
			),
			//write db
			array (
					'server'		=>		'127.0.0.1',
					'port'			=>		'3306',
					'username'		=> 		'root',
					'password'		=>		'',
					'db_name'		=>	 	'72bian'
			),
);

$globals_config = array();
$globals_config['db_config'] 					= 	 	$DATABASE_LIST;
$globals_config['db_table_prefix'] 				= 	 	'm_';
$globals_config['url_type']						= 		1;//0普通模式.1、path模式，eg：host/app/controller/method;2、 rewrte模式，eg：user/new/username/?id=100  3、htm模式，eg：user-add.htm?uid=100,4、兼容模式是普通模式和PATHINFO模式的结合。eg：http://<serverName>/appName/?s=/module/action/id/1/
$globals_config['base_url']						= 		'http://127.0.0.1/framework/';//网址，要带上后面的‘/’
$globals_config['default_layout']				= 		'default'; //布局模式名称，位于更目录public/layout/
$globals_config['default_theme']				= 		'default'; //默认主题名称,位于根目录public/layout/theme
/***************************************************************common config*********************************************/
$globals_config['controller_suffix']			= 		'Controller';
$globals_config['model_suffix']					= 		'Model';
$globals_config['action_suffix']				= 		'_action';
$globals_config['default_app']					= 		'home';
$globals_config['default_controller']			= 		'index';
$globals_config['default_action']				= 		'index';
$globals_config['default_class_extension']		= 		'.class.php';
$globals_config['installed_apps']				= 		'home,blog,user';	//已经安装的应用，如果没有或者非法则会被归为默认的配置，以后会存储在数据库中


$globals_config['error_page']					=		''; //程序出现异常，非调试模式   错误跳转提示页面的URL
$globals_config['error_msg']					= 		''; //程序出现异常，非调试模式显示给用户的友好提示消息
$globals_config['log_level']					=		'toperror,info';//日志记录级别，想记录哪几种就写哪几种。level ,错误等级：info(程序输出信息)，debug（调试输出，证实模式正式关闭），error（一般新错误）、warn（警告性错误）、toperror（严重错误）等等 可以自行定义，
$globals_config['log_handler']					=		'file';//日志记录方式，文件方式
$globals_config['cache_handler']				=		'file';//缓存方式


$globals_config['default_html_cache_time']		= 		300;//渲染页面之后，页面保持5分钟缓存
/***************************************************************common config*********************************************/

