<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require('./class.php');

try {
    $birthday = new Birthday('birthday.json');
    $scheduleLDK = new Schedule('scheduleLDK.json');
    $scheduleMonday = new Schedule('scheduleMonday.json');
    $scheduleUAK = new Schedule('scheduleUAK.json');
    $scheduleUPK = new Schedule('scheduleUPK.json');

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
