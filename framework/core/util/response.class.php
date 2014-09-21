<?php

class response {
	
	public static function response_json($code,$msg,$data=array()){
		if($data===NULL) {
			$data = array();
		}
		
		$return = array(
			'code'=>$code,
			'msg'=>$msg,
		);
		if($data) $return['data'] = $data;
		header("Content-Type:text/html; charset=utf-8");
		exit(json_encode($return));
	}
	
	/**
	 * 优雅输出
	 * 用于程序调试时,完美输出调试数据,功能相当于print_r().当第二参数为true时(默认为:false),功能相当于var_dump()。
	 * 注:本方法一般用于程序调试
	 * @access public
	 * @param array $data         所要输出的数据
	 * @param boolean $option     选项:true或 false
	 * @return array            所要输出的数组内容
	 */
	public static function dump($data, $option = false) {
		//当输出print_r()内容时
		if(!$option){
			echo '<pre>';
			print_r($data);
			echo '</pre>';
		} else {
			ob_start();
			var_dump($data);
			$output = ob_get_clean();
			
			$output = str_replace('"', '', $output);
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
			
			echo '<pre>', $output, '</pre>';
		}
	}
	
	public static function response_404() {
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		echo '404 Not Found!';
		return;
	}
	
	public static function redirect($url){
		if (!headers_sent()) {
			header("Location:" . $url);
		}else {
			echo '<script type="text/javascript">location.href="' . $url . '";</script>';
		}
		exit();
	}
	
	
	/**
	 * 显示提示信息操作
	 *
	 * 所显示的提示信息并非完全是错误信息。如：用户登陆时用户名或密码错误，可用本方法输出提示信息
	 *
	 * 注：显示提示信息的页面模板内容可以自定义. 方法：在项目视图目录中的error子目录中新建message.html文件,自定义该文件内容
	 * 显示错误信息处模板标签为<!--{$message}-->
	 *
	 * 本方法支持URL的自动跳转，当显示时间有效期失效时则跳转到自定义网址，若跳转网址为空则函数不执行跳转功能，当自定义网址参数为-1时默认为:返回上一页。
	 * @access public
	 * @param string $message         所要显示的提示信息
	 * @param string $gotoUrl         所要跳转的自定义网址
	 * @param int    $limitTime     显示信息的有效期,注:(单位:秒) 默认为5秒
	 * @return void
	 */
	public static function show_message($message,$msg_type='info', $limitTime = 5,$gotoUrl = null,$layout=null) {
		//参数分析
		if (!$message) {
			return false;
		}
		//当自定义跳转网址存在时
		if (!is_null($gotoUrl)) {
			$limitTime    = 1000 * $limitTime;
			//分析自定义网址是否为返回页
			if ($gotoUrl == -1) {
				$gotoUrl  = 'javascript:history.go(-1);';
				$message .= '<br/><a href="javascript:history.go(-1);" target="_self">如果你的浏览器没反应,请点击这里...</a>';
			} else{
				//防止网址过长，有换行引起跳转变不正确
				$gotoUrl  = str_replace(array("\n","\r"), '', $gotoUrl);
				$message .= '<br/><a href="' . $gotoUrl . '" target="_self">如果你的浏览器没反应,请点击这里...</a>';
			}
			$message .= '<script type="text/javascript">function doit_redirect_url(url){location.href=url;}setTimeout("doit_redirect_url(\'' . $gotoUrl . '\')", ' . $limitTime . ');</script>';
		}
		//定义base_url加载资源
		$__base_url__ = config('base_url');
		//TODO,根据不同的type显示不同的提示模板
		if($msg_type=='info'){
			$view_file =  ROOT_PATH.'public\assert\html\framework\info.html';
			if(is_file($view_file)){
				ob_start();
				include  $view_file;
				$content = ob_get_clean();
			}else {
				//do nothing.
				$content = '';
			}
		} else {
			exit('error:no message_type template .');
		}
		
		if($layout==null){
			$layout_view = ROOT_PATH.'public/layout/theme/'.config('default_theme').DS.config('default_layout').'.html';
			ob_start();
			include $layout_view;
			$content = ob_get_clean();
		}
		
		if($content){
			exit($content);
		}
	}
	 
}

