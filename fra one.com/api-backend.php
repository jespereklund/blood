<?php


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
	header("HTTP/1.1 200 OK");
	die();
}


//header("Content-type: application/json; charset=utf-8");
//header('Access-Control-Allow-Origin: *');  
//header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Authorization');
//header('Access-Control-Allow-Methods: *');
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");

// backend api  
// https://www.leaseweb.com/labs/2015/10/creating-a-simple-rest-api-in-php/
// https://flettedehvaler.dk/api/v1/api-backend.php/1q2w3e4r5t6y7u8i9o0p/articles/1/delete

//get the user token
$token = $_SERVER["HTTP_AUTHORIZATION"];
  
// get the HTTP method, path and body of the request
//$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// single-user token auth DEPRECATED!!
//$token = array_shift($request);

if ($token != 'TAQ8-YWHH-U34U-HBPE-CS7U-KDDC-PRD5-CQSQ') {
  header("HTTP/1.1 401 Unauthorized");
  exit;
}

// connect to the mysql database
$link = mysqli_connect('flettedehvaler.dk.mysql', 'flettedehvaler_', 'pfTgy5GP', 'flettedehvaler_');
mysqli_set_charset($link,'utf8');
 
// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request);

//hack to get around problems using 'DELETE' method
$action = array_shift($request);
if (isset($action)) {
	if ($action == 'delete') {
		$method = 'DELETE';
	}
}

//hack to get around problems using 'PUT' method
if (isset($key) && $method == 'POST' ) {
	$method = 'PUT';
}
 
$columns = [];
 
// escape the columns and values from the input object
if (is_array($input)) {
	$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
	$values = array_map(function ($value) use ($link) {
	  if ($value===null) return null;
	  return mysqli_real_escape_string($link,(string)$value);
	},array_values($input));
	 
	// build the SET part of the SQL command
	$set = '';
	for ($i=0;$i<count($columns);$i++) {
	  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
	  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
	}	
}

 
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $sql = "select * from `$table`".($key?" WHERE id=$key":''); break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete from `$table` where id=$key"; break;
}
  
// excecute SQL statement
$result = mysqli_query($link,$sql);
 
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die(mysqli_error($link));
}
 
// print results, insert id or affected row count
if ($method == 'GET') {
  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  }
  if (!$key) echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
 
// close mysql connection
mysqli_close($link);