<?php

namespace App\Services\Response;

class ResponseService
{

    private static function responsePrams($status, $errors = [], $data = []) {
        return [
            'status' => $status,
            'errors' => (object)$errors,
            'data' => (object)$data,
        ];
    }

    public static function sendJsonResponse($status, $code = 200, $errors = [], $data = []) {
        return response()->json(
            self::responsePrams($status, $errors, $data),
            $code
        );
    }

    public static function success($data = []) {
        return self::sendJsonResponse(true, 200, [], $data);
    }

    public static function unSuccess($data = []) {
        return self::sendJsonResponse(false, 200, [], $data);
    }
    public static function сreated($data = []) {
        return self::sendJsonResponse(true, 201, [], $data);
    }

    public static function badRequest($data = []) {
        return self::sendJsonResponse(false, 400, [], []);
    }

    public static function unauthorized($data = []) {
        return self::sendJsonResponse(false, 401, [], []);
    }

    public static function forbidden($data = []) {
        return self::sendJsonResponse(false, 403, [], []);
    }

    public static function notFound($data = []) {
        return self::sendJsonResponse(false, 404, [], []);
    }
}
