<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Accept");

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

require ('class.php');

$paths = ['/birthday', '/scheduleMonday', '/scheduleLDK', '/scheduleUAK', '/scheduleUPK', '/temp'];
$key = getUriKey($paths, $_SERVER['REQUEST_URI']);



switch( $key ){
    case "/birthday":
        $birthday = new Birthday('birthday.json');
        $request = new Request( $_SERVER, $_GET, $birthday, file_get_contents('php://input'));
        echo($request->handleRequest());
        break;  

    case "/scheduleMonday":
        $scheduleMonday = new Schedule('scheduleMonday.json');
        $request = new Request( $_SERVER, $_GET, $scheduleMonday, file_get_contents('php://input'));       
        echo($request->handleRequest());
        break;
        
    case "/scheduleLDK":
        $scheduleLDK = new Schedule('scheduleLDK.json');
        $request = new Request( $_SERVER, $_GET, $scheduleLDK, file_get_contents('php://input'));       
        echo($request->handleRequest());
        break; 

    case "/scheduleUAK":
        $scheduleUAK = new Schedule('scheduleUAK.json');
        $request = new Request( $_SERVER, $_GET, $scheduleUAK, file_get_contents('php://input'));       
        echo($request->handleRequest());
        break; 

    case "/scheduleUPK":
        $scheduleUPK = new Schedule('scheduleUPK.json');
        $request = new Request( $_SERVER, $_GET, $scheduleUPK, file_get_contents('php://input'));       
        echo($request->handleRequest());
        break; 

    case "/temp":
        $scheduleUPK = new Schedule('temp.json');
        $request = new Request( $_SERVER, $_GET, $scheduleUPK, file_get_contents('php://input'));       
        echo($request->handleRequest());
        break; 
        
    case 'unknown': 
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 404);
        echo "Страница не существует";
        break;   
}?>