<?php

namespace Antares\Acl\Http;

class AclHttpResponse
{
    public const ERROR = 'error';
    public const SUCCESSFUL = 'successful';

    public static function error($code, $msg = null)
    {
        $msg = !is_null($msg) ? __($msg) : __(AclHttpErrors::getErrorMsg($code));
        return response(json_encode([
            'status' => static::ERROR,
            'error_code' => $code,
            'error_msg' => $msg,
        ]))->header('Content-Type', 'application/json');
    }

    public static function successful($data, $msg = null)
    {
        return response(json_encode([
            'status' => static::SUCCESSFUL,
            'msg' => __($msg),
            'data' => $data,
        ]))->header('Content-Type', 'application/json');
    }
}
