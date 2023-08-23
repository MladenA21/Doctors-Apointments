<?php

//Response helper
class Res
{
    public static function json(array $data, $message = "success", $code = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);

        return json_encode([
            'data' => $data,
            'message' => $message
        ]);
    }
}
