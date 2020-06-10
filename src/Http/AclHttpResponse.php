<?php

namespace Antares\Acl\Http;

class AclHttpResponse
{
    public const ERROR = 'error';
    public const SUCCESSFUL = 'successful';

    protected static function make($status, $code, $message = null, $data = null)
    {
        $r = ['status' => $status];
        if (trim($code) != '') {
            $r['code'] = $code;
        }
        $r['message'] = $message;
        $r['data'] = $data;

        return response(json_encode($r))->header('Content-Type', 'application/json');
    }

    public static function error($code, $message = null, $data = null)
    {
        $message = !is_null($message) ? __($message) : __(AclHttpErrors::getErrorMsg($code));

        return static::make(static::ERROR, $code, $message, $data);
    }

    public static function successful($data, $message = null)
    {
        return static::make(static::SUCCESSFUL, null, $message, $data);
    }
}
