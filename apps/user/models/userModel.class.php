<?php 
	
class userModel extends model {
	
	 
	public function get_by_email($email){ 
		return self::__instance()->get($this->get_table_name('core_user'),'*',array('AND'=>array('email'=>$email,'state'=>1)));
	}
	
	private function get_salt(){ 
		return rand(100, 999);
	}
	
	public function gen_password($password,$salt){ 
		 return md5($password.$salt);
	}
	
	public function register($data){ 
		$db = self::__instance();
		$data['salt'] = $this->get_salt(); 
		$data['password'] = $this->gen_password(trim($data['password']),$data['salt']); 
		$data = array(
				'nickName'=>$data['nick_name'],
				'userPwd'=>$data['password'],
				'salt'=>$data['salt'],
				'email'=>$data['email'],
				); 
		return $db->insert($this->get_table_name('core_user'),$data); 
	}
	
}


?>