<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require ('./class.php');

function getUriKey(array $paths, string $uri): string {
    foreach ($paths as $path) {
        if (strpos($uri, $path) !== false) {
            return $path;
        }
    }

    return 'unknown';
}

$paths = ['/birthday', '/scheduleMonday', '/scheduleLDK', '/scheduleUAK', '/scheduleUPK'];

$key = getUriKey($paths, $_SERVER['REQUEST_URI']);

switch( $key ){
    case "/birthday":

        break;  

    case "/scheduleMonday":

        break;  

    case "/scheduleLDK":

        break;

    case "/scheduleUAK":

        break;

    case "/scheduleLDK":

        break;
        
    case 'unknow': 

        break;   
}?>