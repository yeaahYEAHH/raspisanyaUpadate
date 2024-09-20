<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require('./class.php');

// try {
//     $birthday = new Birthday('birthday.json');
//     $scheduleLDK = new Schedule('scheduleLDK.json');
//     $scheduleMonday = new Schedule('scheduleMonday.json');
//     $scheduleUAK = new Schedule('scheduleUAK.json');
//     $scheduleUPK = new Schedule('scheduleUPK.json');

// } catch (Exception $e) {
//     echo "Ошибка: " . $e->getMessage();
// }



class Request {
    private $data;
    private $body;

    function __construct($data) {
        $this->data = $data;
    }

    function handleRequest() {
        switch ($this->method) {
            case 'GET':
                return $this->handleGetRequest();
            case 'POST':
                return $this->handlePostRequest();
            case 'PUT':
                return $this->handlePutRequest();
            case 'DELETE':
                return $this->handleDeleteRequest();
            default:
                return $this->handleUnknownMethod();
        }
    }

    function handleGetRequest() {
        return json_encode($this->data->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}


switch($_SERVER['REQUEST_URI']){
    case '/birthday':
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 200);
        $birthday = new Birthday('birthday.json');
        $request = new Request($birthday);
        echo $request->handleGetRequest();

        break;  
    
    case '/scheduleLDK':
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 200);
        echo "scheduleLDK";
        break;

    case '/scheduleMonday':
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 200);
        echo "scheduleMonday";

        break;
        
    case '/scheduleUAK':
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 200);

        echo "scheduleUAK";
        break;
        
    default: 
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . 404);
        break;   
}

?>