<?php

// In the real world I would make this index.php file a front-end controller and then
// pass the request off to a routing system. If the documentation didn't specifically
// advise against using a framework, I probably would have used Silex to handle the
// incoming request and route it out to a controller
require_once('../bootstrap.php');

// Call the Controller - in real life I would use a factory to return a controller based on a route
$controller = new \Jimmy\EpicCSVTableViewr\Controller\UserDataController($app, $_GET);

// Write out JSON response
header('Content-Type: application/json');
echo $controller->getJsonResponse();
exit;
