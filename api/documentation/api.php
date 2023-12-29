<?php
require($_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/vendor/autoload.php');
$openapi = \OpenApi\scan($_SERVER['DOCUMENT_ROOT'].'/app-with-api-main/api/controllers');
header('Content-Type: application/json');
echo $openapi->toJSON();