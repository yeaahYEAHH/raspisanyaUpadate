<?php 
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header('HTTP/1.1 405 Method Not Allowed');
    die();
}

if(!(isset($_POST['login']) && isset($_POST['password']))){
    echo "Не заданы поля: логин, пароль";
    header('HTTP/1.1 400 Bad Request');
    die();
}

$str = file_get_contents('auth.txt');
$auth = json_decode($str, true);

if(!($auth['login'] == $_POST['login'] && password_verify($_POST['password'], $auth['password']))){
    header('HTTP/1.1 401 Bad Request');
    header('Location: /admin/index.php');
    die();
}?>