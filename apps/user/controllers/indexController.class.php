<?php
class indexController extends  controller { 
	
	public function _before_action(){
		check_login();
	}
	
	public function _after_action(){
		echo 'after_action';
	}
	
	public function index_action(){
		$this->display();
	}
	
}
