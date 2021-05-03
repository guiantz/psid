<?php
include('config.php');
header("Content-Type:application/json");
session_start();

if (!empty($_GET['sp']))
	$procedure = $_GET['sp'];
else
	$procedure = '';

if (!empty($_GET['p']))
	$param = $_GET['p'];
else
	$param = '';

if (!empty($_GET['json']))
	$json = $_GET['json'];
else
	$json = 'false';


$data = get_data_from_sp($procedure, $param, $json);

if (empty($data)) {
	echo 'Data Not Found';
} else {
	echo $data;
}

function get_data_from_sp($procedure, $param, $json)
{
	// echo var_dump($procedure) . "<br>";
	// echo var_dump($param) . "<br>";
	// echo var_dump($json) . "<br>";

	
	$conn = db_connect($_POST['username'], $_POST['password']);
	if (!$conn) {
		die("Connection Failed: " . mysqli_connect_error());
	}

	$sql = "call " . $procedure . "(" . $param . ")";
	$result = mysqli_query($conn, $sql);
	$rows = array();

	//echo var_dump($result) . "<br>";

	if ($result) {
		if ($json == "true") {
			if (mysqli_num_rows($result) > 0) {
				while ($r = mysqli_fetch_assoc($result)) {
					array_push($rows, $r);
				}
			}
		}
	}
	
	mysqli_close($conn);
	if ($json == "true") {
		return json_encode($rows);
	} else {
		$str = "";
		while ($row = $result->fetch_assoc()) {
			foreach ($row as $column => $value) {
				$str = $str . $column . ":" . $value . ", ";
			}
			$str = $str . ";\n";
		}
		return ($str);
	}
}
?>
