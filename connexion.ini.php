<?php
$servername = "127.0.0.1";
$username = "gerant";
$password = "145azqs87";
$dbname = "Login_DB";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
