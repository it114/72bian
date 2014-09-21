<?php
if (!defined('ACCESS')) exit('Access Denied!');
/**
 * 
 * 日志记录类，改自Thinkphp
 * 
 */
class logFileHandler  {
	
	static $log_cache = array();
	
// 	public static function getInstance(){
// 		return parent::getInstance(get_class());
// 	}
	
	/**
	 * 日志记录缓存，
	 * @param unknown_type $message，记录消息
	 * @param unknown_type $level ,错误等级：info(程序输出信息)，debug（调试输出，证实模式正式关闭），error（一般新错误）、warn（警告性错误）、toperror（严重错误）等等 可以自行定义，
	 * @param unknown_type $record,是否忽略 level过滤机制，强制记录日志，为true强制；为false则会根据level允许的级别来过滤
	 */
	public function record($message,$level = 'info',$record=false) {
		if($record || false !== strpos(config('log_level'),$level)) {
			self::$log_cache[] =   "{$level}: {$message}\r\n";
		}
	}
	
	/**
	 * 保存日子清空缓存
	 * @param unknown_type $destination
	 */
	public function save($destination='') {
		if(empty(self::$log_cache)) return ;
		
		if(empty($destination))
			$destination = ROOT_PATH.'data'.DS.'logs'.DS.date('y_m_d').'.log';
		
		$message    =   implode('',self::$log_cache);
		$this->write_log($message,$destination);
		// 保存后清空日志缓存
		self::$log_cache = array();
	}
	
	/**
	 * log 日志
	 * @param unknown_type $log
	 * @param unknown_type $destination
	 */
	public function write_log($log,$destination='') {
		$now = date('Y-m-d H-i-s');
		$log_path= ROOT_PATH.'data'.DS.'logs'.DS; 
		if(empty($destination))
			$destination = $log_path.date('y_m_d').'.log';
		if(!is_dir($log_path)) { 
			mkdir($log_path,0755,true);
		}
		 
		//检测日志文件大小，超过配置大小则备份日志文件重新生成
		if(is_file($destination) && floor(10240) <= filesize($destination) )
			rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
		error_log("[{$now}] ".$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']."\r\n{$log}\r\n", 3,$destination);
	}
	/****************************************日志功能*******************************/
}