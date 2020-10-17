<?php
/*Setup Connection*/
require_once "../_db-info.php";
$container = array(
	"status" => false,
	"code" => 401,
	"msg" => "Invalid request.",
);

if(isset($_POST['data']) && isset($_POST['table']) && isset($_POST['index'])){
	if($data["db_rules"]["update"]){
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
		
		$rowcontents = "";
		
		foreach($data as $row) {
			foreach($row as $key => $val) {
				if($rowcontents === ""){
					$rowcontents = $key . "='" . $val . "'";
				}else{
					$rowcontents .= ", " . $key . "='" . $val . "'";
				}
			}
		}
		
		$rid = (int)$_POST['index'];
		$rid = $rid - 1;
		
		$sql = "SELECT * FROM " . $_POST['table'] . " LIMIT ". $rid .",1";
		$result = $conn->query($sql);
		
		if(!$result){
			$container["code"] = 404;
			$container["msg"] = "Invalid table name.";
			die(json_encode($container));
		}
		
		if(mysqli_num_rows($result) > 0){
			$id = mysqli_fetch_assoc($result)["id"];
			$sql_n = "UPDATE ". $_POST['table'] ." SET ". $rowcontents . " WHERE id='". $id . "'";
			
			if ($conn->query($sql_n) === TRUE) {
				$container["status"] = true;
				$container["code"] = 200;
				$container["msg"] = "Row updated successfully";
			}else{
				$container["code"] = 401;
				$container["msg"] = "Unable to update row at the moment. Try again later".$sql_n;
			}
		}else{
			$container["code"] = 404;
			$container["msg"] = "Row " . $_POST['index'] . " is not found in table";
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