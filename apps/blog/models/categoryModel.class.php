<?php

class categoryModel extends model {
	
	 
	 
	
	public function get_category_list(){
		$db = self::__instance();
		return   $db->select($this->get_table_name('blog_category'),'*',array('state'=>1,'ORDER'=>'px'));
	}
	
	
}