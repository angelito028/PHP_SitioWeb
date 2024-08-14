<?php 

$host = "localhost";
$bd = "sitio";
$username = "root";
$pass = "";
$port = 3308;

try {
  
  $conn = new PDO("mysql:host=$host:$port;dbname=$bd", $username, $pass);

} catch (Exception $ex) {

  echo $ex -> getMessage();

}
