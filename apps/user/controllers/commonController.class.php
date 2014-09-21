<?php
class commonController extends  controller { 
	
	private $no_session_action = array('register','doregister','logout','login','dologin');
	
	public function _before_action(){ 
		if(in_array($_GET['action'], $this->no_session_action))return;
		check_login();
	}
			 
// 	public function _before_action(){
// 		echo '_before_action';
// 	}
	
// 	public function index_action(){
// 		$this->assign('test','hello view');
// 		$this->display();
// 	}
 	
// 	public function _after_action(){
// 		echo '_after_action';
// 	}
	
	public function register_action(){
		session::clear();
		$this->display();
	}
	
	public function doregister_action(){
		//TODO，验证码
		$email = request::post('email');
		$nick_name = request::post('nick_name'); 
		$password = request::post('password');
		
		if($email&&$nick_name&&$password){ 
			$exists = model('user')->get_by_email($email);
			if($exists){
				response::show_message('用户已存在请登陆！','info',5,url('login'));exit;
			}
			$reg_result = model('user')->register(array('email'=>$email,'nick_name'=>$nick_name,'password'=>$password));
			if($reg_result){ 
				session::set('user_name',$nick_name);
				session::set('user_id',$reg_result);
				response::show_message('注册成功！','info',5,url('index','index','home'));
			} else { 
				response::show_message('注册失败！','info',5,url('register'));
			}
		} else { 
			response::show_message('注册参数不对！','info',5,url('register'));
		}
	}
	
	public function logout_action(){
		session::clear();
		response::redirect(url('login'));
	}
	
	public function login_action(){
		$this->assign('dologin_url',url('dologin'));
		$this->display();
	}
	
	public function dologin_action(){  
		$email = request::post('email');
		$password  = request::post('password');
		if($email&&$password){ 
			$user_model  = model('user'); 
			$exists = $user_model->get_by_email($email); 
			if($exists){   
				$password = $user_model->gen_password($password,$exists['salt']); 
				if($password == $exists['userPwd']){  
					//save session
					session::set('user_name',$exists['nickName']);
					session::set('user_id',$exists['id']);
					response::show_message('登陆成功！','info',5,url('index','index','home'));
				}else {
					response::show_message('Email或密码错误','info',5,url('login'));
				}
			} else {
				response::show_message('用户不存在！','info',5,url('login'));
			}
		} else { 
			response::show_message('参数错误','info',5,url('login'));
		}
	}
	 
}
