<?php 
$url = "./backup/birthday.json";
$data = file_get_contents($url);
$pass = "Neftpk12345!";
$passHash = password_hash($pass, PASSWORD_DEFAULT);
var_dump($passHash);
?>