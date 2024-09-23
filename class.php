<?php 
function getUriKey(array $paths, string $uri): string
{
    foreach ($paths as $path) {
        if (strpos($uri, $path) !== false) {
            return $path;
        }
    }

    return 'unknown';
}

class Request {
    private $body;
    private $get;
    private $data;
    private $uri;
    private $method;


    function __construct( $server, $get, $data, $body ) {
        $this->uri = $server['REQUEST_URI'];
        $this->method = $server['REQUEST_METHOD'];
        $this->get = $get;
        $this->data = $data;
        $this->body = $body;
    }

    function handleRequest() {
        $paths = ["/sort", "/search", "/delete", "/add", "/edit", "/recovery", "/backup", "/current"];

        switch(getUriKey($paths, $this->uri)){
            case "/current":
                if($this->method == "GET"){ 
                    return json_encode($this->current(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }else{
                    header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/sort":
                if($this->method == "GET"){
                    return json_encode($this->sort(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }else{
                    header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/search":
                if($this->method == "GET"){
                    return json_encode($this->search(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }else{
                 header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/delete":
                if($this->method == "DELETE"){   
                    return $this->delete();
                }else{
                   header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/add":
                if($this->method == "POST"){
                    return $this->add();
                }else{
                   header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/edit":
                if($this->method == "PUT"){
                    return $this->edit();
                }else{
                    header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/recovery":
                if($this->method == "GET"){
                    return $this->recovery();
                }else{
                    header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            case "/backup":
                if($this->method == "GET"){
                    return $this->backup();
                }else{
                    header('HTTP/1.1 405 Method Not Allowed');
                }
            break;

            default:
                header('Content-Type: application/json');
                header('HTTP/1.1 ' . 404);
                echo "Страница не существует";
        }
    }

    private function current() {
        try {
            if($this->data instanceof Schedule){
                return $this->data->lesson(date('H:i'));
            }

            return $this->data->search("Date", date('d.m.'));
            
        } catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function search() {
        try {
            if(empty($this->get['field']) || empty($this->get['value'])){
                return throw new Exception("Поля не заданы");
            }

            return $this->data->search($this->get['field'], $this->get['value']);
        } catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function sort() {
        try {
            $field = $this->get['field'];
            $order =  filter_var($this->get['order'], FILTER_VALIDATE_BOOLEAN);
            $date = $this->get['date'];

            if($this->data instanceof Birthday && $date){
                return $this->data->sortDate($date, $order);
            }

            if(empty($field)){
                return throw new Exception("Поля не заданы");
            }

            return $this->data->sort($field, $order);

        } catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function delete() {
        try {
            $list_ID = json_decode($this->body, true); 

            if(!is_array($list_ID) || empty($list_ID)){
                throw new Exception("Не массив или пустой массив");
            }

            $this->data->delete($list_ID);

            header('HTTP/1.1 ' . 201);
            return "Успешно удалено по ID: " . implode(",",$list_ID);

        } catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }
    
    private function add() {
        try {
            $check = json_decode($this->body);
            
            if(!is_object($check) || empty(get_object_vars($check))){
                return throw new Exception("Не объект или пустой объект");
            }

            $obj_add = json_decode($this->body, true);
            $this->data->add($obj_add);

            header('HTTP/1.1 ' . 201);
            return "Успешно добавлен новый объект";
        }

        catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function edit(){
        try {
            $check = json_decode($this->body);
            
            if(!is_object($check) || empty(get_object_vars($check))){
                return throw new Exception("Не объект или пустой объект");
            }

            $obj_edit = json_decode($this->body, true);

            if(!(isset($obj_edit['obj'], $obj_edit['ID']))){
                return throw new Exception("Не существуют поля ID или obj");
            }

            if(!(is_integer($obj_edit['ID'])) || !(is_array($obj_edit['obj']))){
                return throw new Exception("Некоректные значения полей ID или obj");
            }

            $this->data->edit($obj_edit["ID"], $obj_edit["obj"]);

            header('HTTP/1.1 ' . 201);
            return "Успешно отредактирован объект";

        }catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function recovery(){
        try {
            $this->data->recovery();

            return "Успешно восстановлен";

        }catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }

    private function backup(){
        try {
            $this->data->backup();

            return "Успешно сделана резервная копия";

        }catch (Exception $e) {
            header('HTTP/1.1 ' . 400);
            return "Ошибка: " . $e->getMessage();
        }
    }
}

class DataJson {
    protected array $data;
    protected string $url;
    protected string $url_backup;

    function __construct(string $url) {
        $this->url = "./json/".$url;
        $this->url_backup = "./backup/".$url;
        $json = file_get_contents($this->url);

        if(empty($json)){
            return $this->recovery();
        }

        $this->data = json_decode($json, true);
    }

    protected function save( $url ){
        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents( $url, $json);
    }


    function search(string $field, $search) {
        
        $pattern = '/' . preg_quote($search, '/') . '/ui';

        $result = array_filter($this->data, function ($item) use ($field, $search, $pattern) {
            return is_integer($search) ? 
                $item['ID'] === $search : 
                preg_match($pattern, (string) $item[$field]);
        });

        return $result ? array_values($result) : throw new Exception('Ничего не найдено');
    }

    function sort(string $field, bool $order = false){
        usort($this->data, function ($item_a, $item_b) use ($field, $order){
            $a = $item_a[$field];
            $b = $item_b[$field];

            if(empty($a) && empty($b)){
                throw new Exception("Пустые или некорректные значения полей '". $field. "' или '". $field. "' не существует");
            }

            return $order ? $a <=> $b : $b <=> $a;
        });

        return $this->data;
    }

    function delete(array $list_ID){

        foreach($list_ID as $ID){
            if(!isset($this->data[$ID])){
                throw new Exception("Не существует ID: ". $ID. " в списке или задан неверно type: ". gettype($ID)); 
            }

            unset($this->data[$ID]);
        }
        
        $this->data= array_values($this->data);

        for ($i = 0; $i < count($this->data); $i++) {
            $this->data[$i]['ID'] = $i;
        }

        $this->save($this->url);
    }

    function add(array $obj){
        $obj['ID'] = count($this->data);

        $keys1 = array_keys($obj);
        $keys2 = array_keys($this->data[0]);

        if(!($keys1 == $keys2)){
            return throw new Exception("Не существует полей в списке \n Поля добавляемого объекта: ". implode( ",",$keys1). " \n Поля существущих объектов: ". implode( ",",$keys2));
        }


        array_push($this->data, $obj);
        $this->save($this->url);
    }

    function edit(int $ID, array $obj){

        if(!isset($this->data[$ID])){
            throw new Exception("Не существует ID: ". $ID. " в списке или задан неверно type: ". gettype($ID)); 
        }

        $keys1 = array_keys($obj);
        $keys2 = array_keys($this->data[0]);

        if(!($keys1 == $keys2)){
            return throw new Exception("Не существует полей в списке \n Поля изменяемого объекта: ". implode( ",",$keys1). " \n Поля существущих объектов: ". implode( ",",$keys2));
        }

        $this->data[$ID] = $obj;
        $this->save($this->url);
    }


    private function recoveryFile($url, $url_backup) {
        $content = file_get_contents($url_backup);
        if (!$content) {
            return throw new Exception("Не удалось прочитать файл ". $url);
        }

        $result = file_put_contents($url, $content);
        if (!$result) {
            return throw new Exception("Не удалось записать файл ". $url_backup);
        }
    }
    
    function backup() {

        if (!file_exists($this->url_backup)) {
            touch($this->url_backup);
        }

        $this->recoveryFile($this->url_backup, $this->url);
    }

    function recovery() {

        if (!file_exists($this->url)) {
            touch($this->url);
        }

        $this->recoveryFile($this->url, $this->url_backup);
    }
};

class Birthday extends DataJson{
    function sortDate(string $date, bool $order = true){
        $list = ["Day", "Month", "Year"];
        if(!in_array($date, $list)){
            throw new Exception("Не существует <". $date. ">");
        }
        usort($this->data, function ($a, $b) use ($date) {
            $a_field = DateTime::createFromFormat('d.m.Y', $a['Date']);
            $b_field = DateTime::createFromFormat('d.m.Y', $b['Date']);
            switch ($date){
                case 'Day': 
                    $date_a = $a_field->format('d');
                    $date_b = $b_field->format('d');
                    break;
                case 'Month': 
                    $date_a = $a_field->format('m');
                    $date_b = $b_field->format('m');
                    break;
                case 'Year': 
                    $date_a = $a_field->format('Y');
                    $date_b = $b_field->format('Y');
                    break;
            } 

            return $order ? $date_a <=> $date_b :  $date_b <=> $date_a;
        });

        return $this->data;
    }
}

class Schedule extends DataJson{

    function lesson(string $time){
        foreach($this->data as $lesson){    
            if($lesson['timeEnd'] > $time && $lesson['timeStart'] <= $time){
                return $lesson;
            }
        }

        throw new Exception('Ничего не найдено');
    }
}
?>