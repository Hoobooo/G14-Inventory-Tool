<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->email) && !empty($data->password)) {
    $user->username = $data->username;
    $user->email = $data->email;
    $user->password = $data->password;
    $user->role_id = $data->role_id ?? 2; // Default role

    if($user->register()) {
        http_response_code(201);
        echo json_encode(array("message" => "User registered successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Unable to register user."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register user. Data is incomplete."));
}