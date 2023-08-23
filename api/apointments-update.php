<?php

require "./autoload.php";
$db = new Database("pabau");


if($_SERVER['REQUEST_METHOD'] == "POST") {
    $validator = new Validator($_POST);

    $validator->validate([
        'day' => 'required',
        'start_time' => "required",
        'end_time' => "required",
    ]);


    $db->edit($_POST['id'], "apointments", [
        'day' => $_POST['day'],
        'start_time' => $_POST['start_time'],
        'end_time' => $_POST['end_time'],
    ]);
    
    echo Res::json([], "successfully created record");
}