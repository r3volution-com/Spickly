<?php 
class comments {

	public $table = $_GET["p"];
	public $text = $_GET["text"];
	public $id 	= $_GET["id"];
	
	public function insert ($table,$text, $id) {
	
		if($this->tableExists("comments"_$table)){
	
		$res = 'INSERT * INTO '.$table.' (member_id_send, to_photo_id, comment) VALUES ('.$user_row["member_id"].', '.$id.', '.$text.')' or die(mysql_error());
		
		}else{
	
		die "La tabla no existe";
		
		} 
	}
}

