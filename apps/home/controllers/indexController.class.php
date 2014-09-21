<?php
class indexController extends  controller { 
	
	public function _before_action(){
		check_login();
	}
	
	public function index_action(){
		$this->assign('page_title','主页');
		$this->assign('user_name',session::get('user_name'));
		$this->display();
	}
	
	
	 
}
