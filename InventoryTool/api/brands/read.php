<?php
header("Content-Type: application/json");

include_once '../../config/database.php';
include_once '../../models/Brand.php';


$database = new Database();
$db = $database->connect();
$brand = new Brand($db);

$result = $brand->read();
$brands = $result->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($brands);