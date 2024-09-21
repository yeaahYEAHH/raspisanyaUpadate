<?php 

class DataJson {
    protected array $data;
    protected string $url;
    protected string $url_backup;

    function __construct(string $url) {
        $this->url = "./json/".$url;
        $this->url_backup = "./backup/".$url;
        $json = file_get_contents($this->url);

        if(empty($json)){
            $this->recovery($this->url, $this->url_backup);
            throw new Exception("Восстановление файла");
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

            if(empty($a) && empty($b)){
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


    function recovery($url, $url_backup) {
        try {
            $content = file_get_contents($url_backup);
            if (!$content) {
                throw new Exception("Не удалось прочитать файл ". $url);
            }

            $result = file_put_contents($this->url, $content);
            if (!$result) {
                throw new Exception("Не удалось записать файл ". $url_backup);
            }

        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
        }
    }
    
    function backup() {
        $this->recovery($this->url_backup, $this->url);
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
?>