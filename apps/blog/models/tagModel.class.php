<?php

class tagModel extends model  { 
	
	
	public function get_tag_list(){
		$db = self::__instance();
		$table_prefix = config('db_table_prefix');
		$tag_table_name  = $this->get_table_name('tags');
		$tag_blog_tag_name = $this->get_table_name('blog_tag');
		$sql = "select  id,tagname,count_blog  from  $tag_table_name ,$tag_blog_tag_name 
		  where $tag_blog_tag_name.tagId=$tag_table_name.id group by $tag_table_name.id order by $tag_table_name.count_blog";
        return $db->query($sql)->fetchAll();
	}
	
	public function get_by_id($tag_id){
		$db = self::__instance();
		return $db->get($this->get_table_name('tags'),'*',array('id'=>$tag_id));
	}
	
	public function get_article_ids_by_tag_id($tag_id){
		$db = self::__instance();
		$article_ids =  $db->select($this->get_table_name('blog_tag'),'blogId',array('tagId'=>$tag_id));
		return $article_ids;
	}
	
	
	
}