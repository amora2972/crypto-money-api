<?php

namespace App\Http\Helpers;

Class CustomResponse
{

    public static $successCode = 200;
    public static $errorCode = 500;
    public static $unAuthorized = 401;
    public static $notFound = 404;
    public static $unprocessableEntity = 422;

    public static function customResponse($result, $code, $message = null)
    {
        $response = new \stdClass();

        $response->success = self::$successCode == $code ? true : false;

        $response->statusCode = $code;

        if ($message != null && $message != "")
            $response->message = $message;

        if($code != self::$notFound)
            $response->result = $result;

        return response()->json($response);
    }

}
