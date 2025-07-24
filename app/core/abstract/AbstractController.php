<?php

namespace AppDAF\ABSTRACT;

use AppDAF\CORE\Singleton;
use AppDAF\ENTITY\ResponseEntity;

abstract class AbstractController extends Singleton
{

    protected function renderJson(ResponseEntity $response): void
    {
        http_response_code($response->code);

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header('Content-Type: application/json');

        echo $response->toJson();
    }
}
