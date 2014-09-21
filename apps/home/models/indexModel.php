<?php 
	
class indexModel extends model {
	
	private $table_name = "article";
	private function get_table_name() {
		return DB_TABLE_PREFIX.$this->table_name;
	}
	
	public function slect_latest($offset=0,$len=15) {
		$db = self::__instance();
		$ret =  $db->select($this->get_table_name(),'*',array('ORDER'=>'createTime desc','state'=>1,'LIMIT'=>array($offset,$len)));
		//lib_common_functions::echoVars($ret);
		//echo $db->last_query();
		return $ret;
	}
	
	
	public function slect_hot($offset=0,$len=15) {
		$db = self::__instance();
		$ret =  $db->select($this->get_table_name(),'*',array('ORDER'=>'countReply desc,countFavorite desc ,createTime desc','state'=>1,'LIMIT'=>array($offset,$len)));
		//lib_common_functions::echoVars($ret);
		//echo $db->last_query();
		return $ret;
	}
	
	
}


?>