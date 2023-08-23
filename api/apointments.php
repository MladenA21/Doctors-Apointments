<?php

require "./autoload.php";
$db = new Database("pabau");


if($_SERVER['REQUEST_METHOD'] == "POST") {
    $validator = new Validator($_POST);

    $validator->validate([
        'name' => 'required|max:100',
        'email' => 'required|email',
        'number' => 'required|phone',
        'special_requirements' => 'required',
        'medical_condition' => 'required',
        'service_id' => 'required',
        'status' => "required",
        'day' => 'required',
        'start_time' => "required",
        'end_time' => "required",
    ]);


    $db->insert("apointments", $_POST);
    echo Res::json([], "successfully created record");
} else if($_SERVER['REQUEST_METHOD'] == "GET") {
    if(isset($_GET['cancel'])) {
        $db->destroy($_GET['cancel'], 'apointments');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die;
    }


    $appointments = $db->select("apointments")->get();
    echo Res::json($appointments);
}