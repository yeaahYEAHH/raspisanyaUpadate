<?php

if($_SERVER['REQUEST_URI'] == '/'){
    header("Location: /front/");
    die();
}

$token = file_get_contents("token.txt");


if($_SERVER['HTTP_AUTHORIZATION'] != "Bearer ".$token){
    echo "Ошибка: Не авторизован";
    header("HTTP/1.1 401 Unauthorized");
    die();
}

header('HTTP/1.1 307 Temporary Redirect');
header("Location: /server.php".$_SERVER['REQUEST_URI']);
exit;
?>