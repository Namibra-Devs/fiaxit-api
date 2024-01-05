<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once $_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/controllers/pages.php';

$database = new Database();
$db = $database->getConnection();

$item = new Pages($db);
$data = json_decode(file_get_contents('php://input'));
$item->user_role = $data->user_role;
$item->phone_no = $data->phone_no;
$item->email = $data->email;
$item->password = $data->password;

$response = $item->signup();
// print_r($response);
// echo $response["status"];
// exit;

if ($response["status"] === "1"):
    http_response_code(200);
    echo json_encode([
        "type" => "error",
        "title" => "error",
        "message" => "User already exists"
    ]);
elseif ($response["status"] === "2"):
    http_response_code(200);
    echo json_encode([
        "type" => "success",
        "title" => "success",
        "message" => "Signup successful"
    ]);
else:
    http_response_code(404);
    echo json_encode([
        "type" => "danger",
        "title" => "Failed",
        "message" => "Signup failed. Please try again"
    ]);
endif;
