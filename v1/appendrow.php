<?php
/*Setup Connection*/
require_once "../_db-info.php";
$container = array(
	"status" => false,
	"code" => 401,
	"msg" => "Invalid request.",
);

if(isset($_POST['data']) && isset($_POST['table'])){
	if($data["db_rules"]["create"]){
		$conn = new mysqli("localhost", $data["db_info"]["username"], $data["db_info"]["password"], $data["db_info"]["dbname"]);
		
		if ($conn->connect_error) {
			$container["code"] = 500;
	        $container["msg"] = "Unable to connect with mysql database. Please check connection details and try again.";
			die(json_encode($container));
		}
		
		$data = @json_decode('[' . $_POST['data'] . ']',true);
		
		if($data === null && json_last_error() !== JSON_ERROR_NONE){
			$container["code"] = 401;
			$container["msg"] = "Bad JSON format. Check and try again";
			die(json_encode($container));
		}
		
		if(@array_keys($data[0]) === null){
			$container["code"] = 401;
			$container["msg"] = "Bad JSON format. Check and try again";
			die(json_encode($container));
		}
		
		$columns = "id";
		$values = "'". uniqid() ."'";
		
		foreach($data as $row) {
			foreach($row as $key => $val) {
				$columns .= "," . $key;
				$values .= "," .  "'" .$val . "'";
			}
		}
		
		$sql = "INSERT INTO ". $_POST['table'] ." (". $columns .") VALUES (". $values .")";
		
		if ($conn->query($sql) === TRUE) {
			$container["status"] = true;
			$container["code"] = 200;
			$container["msg"] = "New row added successfully";
		}else{
			$container["code"] = 401;
			$container["msg"] = "Unable to craete new row at the moment. Try again later";
		}
		
		
	}else{
		$container["code"] = 403;
	    $container["msg"] = "Permission denied.";
	}
}else{
	$container["code"] = 401;
	$container["msg"] = "Invalid request. Row index/Table name not found in parameter.";
}

echo json_encode($container);

?>