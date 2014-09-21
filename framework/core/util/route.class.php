<?php
if (!defined('ACCESS')) exit('Access Denied!');
class route  {
		
	/**
	 * 路由分发-路由分发核心函数
	 * 1. 判断是否开启分发
	 * 2. 获取request信息
	 * 3. 解析URI
	 * 
	 */
	public  function start() { 
		$filter_param = array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e');
		$uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']); 
		switch (config('url_type')) {
			case 1 :
				$request = $this->getRequest($uri);
				$this->parsePathUri($request); 
				break;
					
			case 2:
				$request = $this->getRequest($uri);
				$this->parseRewriteUri($request);
				break;
					
			case 3 :
				$request = $this->getRequest($uri);
				$this->parseHtmlUri($request);
				break;
				
			case 4:
				$this->parseCompatibleUri($uri);
				break;
			default :
				return false;
				break;
		}
		
		if(!isset($_GET['app'])){
			$_GET['app'] = config('default_app');
		}
		
		if(!isset($_GET['controller'])){
			$_GET['controller'] = config('default_controller');
		}
		
		if(!isset($_GET['action'])){
			$_GET['action'] = config('default_action');
		}
		$installed_apps = explode(',', config('installed_apps'));
		if(!in_array($_GET['app'], $installed_apps)){
			exit('404 app not found !');
		}
		return true;
	}
	
	
	/**
	 * 兼容模式是普通模式和PATHINFO模式的结合。
	 * eg：http://<serverName>/appName/?s=/module/action/id/1/
	 * @param unknown_type $request
	 */
	private function parseCompatibleUri($request){
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == ''||empty( $_GET['s'])) return false;
		$array_tmp_uri = preg_split('[\\/]', $_GET['s'], -1, PREG_SPLIT_NO_EMPTY);
		$_GET['app'] 		 = @ $array_tmp_uri[0];
		$_GET['controller']  = @ $array_tmp_uri[1];
		$_GET['action']      = @ $array_tmp_uri[2];
	}
	
	/**
	 * 路由分发，获取Uri数据参数
	 * 1. 对Service变量中的uri进行过滤
	 * 2. 配合全局站点url处理request
	 * @return string
	 */
	private function getRequest($uri) {
		$posi = strpos($uri, '?');
		if ($posi) $uri = substr($uri,0,$posi);
		$urlArr = parse_url(config('base_url'));
		$request = str_replace(trim($urlArr['path'], '/'),'', $uri);
		if (strpos($request, '.php')) {
			$request = explode('.php', $request);
			$request = $request[1];
		}
		return $request;
	}
	
	/**
	 * 解析Path Uri
	 * 1. 解析index.php/user/new/username
	 * eg:http://127.0.0.1/shf/user/user/add/name/zxy/age/23/sex/1
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parsePathUri($request) {
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		
		if (isset($request[0])) $_GET['app'] = $request[0];
		if (isset($request[1])) $_GET['controller'] = $request[1];
		if (isset($request[2])) $_GET['action'] = $request[2];
		
		unset($request[0], $request[1], $request[2]);
		if (count($request) > 1) {
			$mark = 0;
			$val = $key = array();
			foreach($request as $value){
				$mark++;
				if ($mark % 2 == 0) {
					$val[] = $value;
				} else {
					$key[] = $value;
				}
			}
			if(count($key) !== count($val)) $val[] = NULL;
			$get = array_combine($key,$val);
			foreach($get as $key=>$value) $_GET[$key] = $value;
		}
		return $request;
	}

	/**
	 * 解析rewrite方式的路由
	 * 1. 解析index.php/user/new/username/?id=100
	 * eg:http://127.0.0.1/shf/index.php/user/user/add/?uid=100或者http://127.0.0.1/shf/user/user/add/?uid=100
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parseRewriteUri($request) {
		if (!$request) return false;
		$request =  trim($request, '/');
		if ($request == '') return false;
		$request =  explode('/', $request);
		if (!is_array($request) || count($request) == 0) return false;
		 
		if (isset($request[0])) $_GET['app'] = $request[0];
		if (isset($request[1])) $_GET['controller'] = $request[1];
		if (isset($request[2])) $_GET['action'] = $request[2];
		
		return $request;
	}
	
	/**
	 * 解析html方式的路由
	 * 1. 解析user-add.htm?uid=100
	 * eg:http://127.0.0.1/shf/index.php/user-add.htm?uid=100
	 * 2. 解析成数组，array()
	 * @param string $request
	 */
	private function parseHtmlUri($request) {
		if (!$request) return false;
		$request = trim($request, '/');
		$request = str_replace('.htm', '', $request);
		if ($request == '') return false;
		$request = explode('-', $request);
		if (!is_array($request) || count($request) == 0) return false;
		
		if (isset($request[0])) $_GET['app'] = $request[0];
		if (isset($request[1])) $_GET['controller'] = $request[1];
		if (isset($request[2])) $_GET['action'] = $request[2];
		
		return $request;
	}
	
	
}