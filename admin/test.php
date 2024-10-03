<?php 
$pass = "Neftpk12345";
$hash = password_hash( $pass, PASSWORD_DEFAULT);
echo $hash;
?>