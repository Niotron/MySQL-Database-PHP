<?php
/*Setup Connection*/
require_once "../_db-info.php";
$container = array(
	"status" => false,
	"code" => 0,
	"msg" => "",
);

if(isset($_GET['index']) && isset($_GET['table'])){
	if($data["db_rules"]["read"]){
		$conn = new mysqli("localhost", $data["db_info"]["username"], $data["db_info"]["password"], $data["db_info"]["dbname"]);
		if ($conn->connect_error) {
			$container["code"] = 500;
	        $container["msg"] = "Unable to connect with mysql database. Please check connection details and try again.";
			die(json_encode($container));
		}
		
		$rid = (int)$_GET['index'];
		$rid = $rid - 1;
		
		$sql = "SELECT * FROM " . $_GET['table'] . " LIMIT ". $rid .",1";
		$result = $conn->query($sql);
		
		if(!$result){
			$container["code"] = 404;
			$container["msg"] = "Invalid table name.";
			die(json_encode($container));
		}
		
		if(mysqli_num_rows($result) > 0){
			$container["status"] = true;
			$container["code"] = 200;
			$container["msg"] = "Successfully got row data";
			
			$row_content = mysqli_fetch_assoc($result);
			$rid = $row_content["id"];
			unset($row_content["id"]);
			
			$rowcontent = array(
				"rowid" => $rid,
				"fields" => $row_content
			);
			
			$datas = array();
			array_push($datas,$rowcontent);
			$container["data"] = $datas;
		}else{
			$container["code"] = 404;
			$container["msg"] = "Row " . $_GET['index'] . " is not found in table";
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