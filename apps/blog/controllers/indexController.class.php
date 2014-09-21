<?php

class indexController extends  baseController { 
	
	public function index_action(){
		//display hot article .
		$hot_article_list = model('article')->get_hot_article_list();
		$this->assign('article_list',$hot_article_list);
		$this->display();
	}
	
	
}
