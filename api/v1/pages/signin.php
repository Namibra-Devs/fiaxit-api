<?php

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");

include_once $_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/controllers/pages.php';

$database = new Database();
$db = $database->getConnection();

$item = new Pages($db);
$data = json_decode(file_get_contents('php://input'));
$item->email = $data->email;
$item->password = $data->password;

$response = $item->signin();

if($response["status"] === "2"):
    http_response_code(200);
    echo json_encode(
        array(
            "type"=>"success",
            "title"=>"Success",
            "user"=>$response["user"]
        )
    );
elseif ($response["status"] === "3"):
        http_response_code(404);
        echo json_encode([
            "type" => "error",
            "title" => "error",
            "message" => $response["message"]
        ]);    
elseif ($response["status"] === "4"):
        http_response_code(404); 
        echo json_encode([
            "type" => "error",
            "title" => "error",
            "message" => $response["message"]
        ]);    
else:
    http_response_code(404);
    echo json_encode(
        array(
            "type"=>"danger",
            "title"=>"Failed",
            "message"=>"signin failed. Please try again."
        )
    );
endif;

