<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$scheduleFile = '/schedule.json'; 
$scheduleString = file_get_contents($scheduleFile);
$scheduleData = json_decode($scheduleString, true);

$birthdayFile = '/birthday.json'; 
$birthdayString = file_get_contents($birthdayFile);
$birthdayData = json_decode($birthdayString, true);

class DataJson {
    protected array $data;
    protected string $url;
    protected string $url_backup;

    function __construct(string $url) {
        $this->url = "./json/".$url;
        $this->url_backup = "./backup/".$url;
        $json = file_get_contents($this->url);

        if(empty($json)){
            throw new Exception("Восстановите файл");
            return;
        }

        $this->data = json_decode($json, true);
    }

    protected function save( $url ){
        $this->data = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents( $url, $this->data);
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

    function sort(string $field, bool $order = true){
        usort($this->data, function ($item_a, $item_b) use ($field, $order){
            $a = $item_a[$field];
            $b = $item_b[$field];

            if(empty($a) || empty($b)){
                throw new Exception("Пустые значение");
            }

            return $order ? $a <=> $b : $b <=> $a;
        });

        return $this->data;
    }

    function delete(array $list_ID){
        if(empty($list_ID)){
            throw new Exception("Элемент не существует");
        }

        foreach($list_ID as $ID){
            unset($this->data[$ID]);
        }

        $this->data= array_values($this->data);

        for ($i = 0; $i < count($this->data); $i++) {
            $this->data[$i]['ID'] = $i;
        }

        $this->save($this->url);
    }

    function add(object $obj){
        $obj['ID'] = count($this->data);
        array_push($rhis->data, $obj);
        $this->save($this->url);
    }

    function edit(int $ID, object $obj){
        $this->data[$ID] = $obj;
        $this->save($this->url);
    }

    function backup(){
        $file = fopen($this->url_backup, "w");
        $json =  json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!$file) {
            throw new Exception("Не удалось открыть файл для записи");
        }

        fwrite($file, $json);
        fclose($file);
    }

    function recovery(){
        $file = fopen($this->url, "w");
        $loc = file_get_contents($this->$url_backup);
        print_r($loc);
        // $json =  json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // if (!$file) {
        //     throw new Exception("Не удалось открыть файл для записи");
        // }

        // fwrite($file, $json);
        // fclose($file);
    }
};

class Birthday extends DataJson{

    function sortDate(string $date, bool $order = true){
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
                default: 
                    return "Не существеющая дата"; 
                    break;
            } 

            return $order ? $date_a <=> $date_b :  $date_b <=> $date_a;
        });

        return $this->data;
    }
}

class Schedule extends DataJson{

    function current(string $time){
        foreach($this->data as $lesson){
            if($lesson['timeEnd'] > $time){
                return $lesson;
            }
        }
    }
}



try {
    $dataJson = new Birthday('birthday.json');
    $dataJson->recovery();
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
// $scheduleLDK = new Schedule( .'/scheduleLDK.json');


// var_dump($scheduleLDK->current("10:30"));




// Вывод результата

// function getActualityLesson( $array, $time){
//     for ($i = 0; $i < count($array); $i++) {
//         if($array[$i]['timeEnd'] > $time){
//             return $array[$i];
//         }
//     }   
// }

// function getActualityBirthday( $date, $array ){
//     $nameArray = [];
//     for ($i = 0; $i < count($array); $i++) {
//         $dateBirthday = preg_replace('/.{5}$/', '', $array[$i]['Date']);
//         $name = $dateBirthday == $date ? $array[$i]['Name'] : false;
//         if( $name ){
//             array_push($nameArray, $name);
//         }
//     }
//     return $nameArray;
// }

// function sortByField($array, $field, $order = 'asc', $date = 'Year') {
//     usort($array, function ($a, $b) use ($field, $order, $date) {
//         if ($field === 'Date') {
//             $aField = DateTime::createFromFormat('d.m.Y', $a[$field]);
//             $bField = DateTime::createFromFormat('d.m.Y', $b[$field]);

//             if (!$aField || !$bField) {
//                 return 0; // если одна из дат некорректна, можно не изменять порядок
//             }

            
//             $a = $aField->format('Y');
//             $b = $bField->format('Y');
//             // Сравнение по месяцу
//             if($date == 'Day'){
//                 $a = $aField->format('d');
//                 $b = $bField->format('d');
//             }
//             if($date == 'Month'){
//                 $a = $aField->format('m');
//                 $b = $bField->format('m');
//             }


//             if ($order === 'asc') {
//                 return $a <=> $b;
//             } else {
//                 return $b <=> $a;
//             }
//         } else {
//             $aField = $a[$field];
//             $bField = $b[$field];
//         }
//         if ($order === 'asc') {
//             return $aField <=> $bField;
//         } else {
//             return $bField <=> $aField;
//         }
//     });

//     return $array;
// }



// function search($array, $field, $searchField){
//     $result = array_filter($array, function ($item) use ($field, $searchField) {
//         if (isset($item[$field])) {
//             return stripos($item[$field], $searchField) !== false;
//         }
//         return false;
//     });

//     if (!empty($result)) {
//         $result = array_values($result);
//         return [$result[0]];
//     } else {
//         return ['none'];
//     }
// }



// function delete($array, $id){
//     unset($array[$id]);

//     $array = array_values($array);
//     for ($i = 0; $i < count($array); $i++) {
//         $array[$i]['ID'] = $i;
//     };

//     return $array;
// }

// function update($array, $id, $newObj){
//     $newObj['ID'] = $id;
//     $array[$id] = $newObj;

//     return $array;
// }

// function add($array, $newObj){
//     $newObj['ID'] = count($array);
//     array_push($array, $newObj);

//     return $array;
// }

// $postData = file_get_contents('php://input');
// $data = json_decode($postData, true);

// switch ($data['type']) {
//     case 'get':
//         if($data['department'] !== null){
//             echo json_encode( $scheduleData[$data['department']], JSON_UNESCAPED_UNICODE);
//         }else{
//             echo json_encode( $birthdayData, JSON_UNESCAPED_UNICODE);
//     }break;

//     case 'sort':
//         if($data['department'] !== null){
//             $obj = sortByField($scheduleData[$data['department']], $data['field'], $data['order']);
//             echo json_encode( $obj, JSON_UNESCAPED_UNICODE);
//         }else{
//             $obj = sortByField($birthdayData, $data['field'], $data['order'], $data['date']);
//             echo json_encode( $obj , JSON_UNESCAPED_UNICODE);
//         }break;

//     case 'search':
//         if($data['department'] !== null){
//             $obj = search($scheduleData[$data['department']], $data['field'], $data["searchField"]);
//             echo json_encode( $obj, JSON_UNESCAPED_UNICODE);
//         }else{
//             $obj = search($birthdayData, $data['field'], $data['searchField']);
//             echo json_encode( $obj, JSON_UNESCAPED_UNICODE);    
//         }break;

//     // изменение файла
//     case 'delete': 
//         if($data['department'] !== null){
//             $obj =  delete($scheduleData[$data['department']], $data['ID']);
//             $scheduleData[$data['department']] = $obj;
//             $json = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
//             file_put_contents($scheduleFile, $json);
//         }
        
//         else{
//             $obj = json_encode(delete($birthdayData, $data['ID']), JSON_UNESCAPED_UNICODE);
//             file_put_contents($birthdayFile, $obj);
//         }break;

//     case 'update': 
//         if($data['department'] !== null){
//             $obj = update($scheduleData[$data['department']],$data['ID'], $data['obj']);
//             $scheduleData[$data['department']] = $obj;
//             $json = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
//             file_put_contents($scheduleFile, $json);
//         }else{
//             $obj = json_encode( update($birthdayData, $data['ID'], $data['obj']), JSON_UNESCAPED_UNICODE);
//             file_put_contents($birthdayFile, $obj);
//         }break;

//     case 'add': 
//         if($data['department'] !== null){
//             $obj = add($scheduleData[$data['department']], $data['obj']);
//             $scheduleData[$data['department']] = $obj;
//             $json = json_encode($scheduleData, JSON_UNESCAPED_UNICODE);
//             file_put_contents($scheduleFile, $json);
//         }else{
//             $obj = json_encode( add($birthdayData, $data['obj']), JSON_UNESCAPED_UNICODE);
//             file_put_contents($birthdayFile, $obj);
//         }break;
// }

// function getObjByDepartment($department){
//     global $scheduleData;
//     global $birthdayData;

//     $lesson = getActualityLesson($scheduleData[$department], date('H:i'));
//     $birthday = getActualityBirthday( date('d.m'), $birthdayData );

//     $dateNow = new DateTime(date('H:i:s'));
//     $dateEnd = new DateTime($lesson['timeEnd']);
//     $interval = $dateEnd->diff($dateNow);

//     $sec = $interval->s;
//     $min = $interval->i;

//     $sec = $sec >= 10 ? $sec : "0".$sec;
//     $min = $min >= 10 ? $min : "0".$min;

//     $objGetResponse = [
//         "lesson" => $lesson,
//         "birthday" => $birthday,
//         "duration" => $min.":". $sec,
//     ];

//     return $objGetResponse;
// }

// print_r($_SERVER['REQUEST_METHOD']);


// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'getActuality') {
 
//     if( isset($_POST['title'])){
//         echo json_encode( getObjByDepartment($_POST['title']), JSON_UNESCAPED_UNICODE);
// return;
//     }
    
//     $return = date('l') ==  'Monday' ? getObjByDepartment("scheduleMonday") : getObjByDepartment("scheduleUAK");
//     $allObj = [
//         "scheduleUAK" => $return,
//         "scheduleLDK" => getObjByDepartment("scheduleLDK"),
//         "scheduleUPK" => getObjByDepartment("scheduleUPK")
//     ];

//     echo json_encode( $allObj , JSON_UNESCAPED_UNICODE);
// };
?>
