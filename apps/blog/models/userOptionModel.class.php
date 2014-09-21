<?php

class userOptionModel  extends Model {
	
	 
	
	public function get_user_option($uid,$key=null){  
		//TODO检测缓存，
		$db = self::__instance();
		$data_list =  $db->select($this->get_table_name('blog_options'),'*',array('uid'=>$uid));
		if($key!=null){
			if($data_list&&key_exists($key, $data_list)){
				return $data_list[$key];
			}else {
				return false;
			}
		}
		if(is_array($data_list)){
			//key - value 处理
			$return_array = array();
			foreach ($data_list  as $key => $value){
				$return_array[$value['key']] = $value['value'];
			}
			return $return_array;
		}else {
			return false;
		}
	}
	
}