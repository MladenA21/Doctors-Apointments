<?php
require "./autoload.php";
$db = new Database("pabau");

$services = $db->select("services")->get();

echo Res::json($services);