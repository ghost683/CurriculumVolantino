<?php

namespace App\Core\Http;

class Response {

    public function responseError($errorCode, $message, $debug) {

        $error = [
            "success" => false,
            "code" => $errorCode,
            "error" => [
                "message" => $message,
                "debug" => $debug
            ]
        ];
        echo json_encode($error);
        exit;
    }

    public function responseSuccess($results){
        $response = [
            "success" => true,
            "code" => 200,
            "results" => $results
        ];
        $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
        echo json_encode($response);
        exit;
    } 
}