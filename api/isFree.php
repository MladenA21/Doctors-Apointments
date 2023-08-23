<?php
require "./autoload.php";
$db = new Database("pabau");

if(isset($_GET['without']) && $_GET['without'] != "") {
    $apointments = $db->select("apointments", ['start_time', 'end_time'], condition: "WHERE day = '". $_POST['day']. "' AND id != '".$_GET['without']."'")->orderBy("start_time", "ASC")->get();
} else {
    $apointments = $db->select("apointments", ['start_time', 'end_time'], condition: "WHERE day = '". $_POST['day']. "'")->orderBy("start_time", "ASC")->get();
}


$hoursFree = [];
for($i=9; $i<=16; $i++) {
    array_push($hoursFree, $i);
}

foreach($apointments as $apointment) {

    for($i=$apointment['start_time'] - 9; $i < $apointment['end_time'] - 9; $i++) {
        $hoursFree[$i] = 0;
    }
}


$result = [];
for($i=0; $i < count($hoursFree); $i++) {
    if($hoursFree[$i] != 0) {
        array_push($result, $hoursFree[$i]);
    }
}

echo Res::json([
  'freeHours' =>  $result,
  'takenIntervals' => $apointments
], 'succesfully fetched items', 200);