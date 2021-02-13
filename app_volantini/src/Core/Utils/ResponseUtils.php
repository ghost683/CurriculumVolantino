<?php

namespace App\Core\Utils;

class ResponseUtils {

    public static function formatResponseError($errorCode, $message, $debug = "") {

        $error = [
            "success" => false,
            "code" => $errorCode,
            "error" => [
                "message" => $message,
                "debug" => $debug
            ]
        ];
        return $error;
    }

    public static function formatResponseSuccess($results){
        $response = [
            "success" => true,
            "code" => 200,
            "results" => $results
        ];
        return $response;
    } 
}