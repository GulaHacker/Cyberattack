<?php

$servername = "localhost"; 
$dbUsername = "root";
$dbPassword = "";
$dbName = "register_jabatan_agensi";

$conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbName);

//Check connection DB
if (!$conn) {
    die("Connection failed: ".mysqli_connect_error());
}
