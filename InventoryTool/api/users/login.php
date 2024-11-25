<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/User.php';
include_once '../../utils/Session.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->password)) {
    $result = $user->login($data->username, $data->password);
    
    if($result) {
        Session::start();
        Session::set('user_id', $result['id']);
        Session::set('username', $result['username']);
        Session::set('role_id', $result['role_id']);

        http_response_code(200);
        echo json_encode(array(
            "message" => "Login successful.",
            "user" => array(
                "id" => $result['id'],
                "username" => $result['username'],
                "role_id" => $result['role_id']
            ),
            "session_id" => session_id()
        ));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed. Invalid username or password."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
}