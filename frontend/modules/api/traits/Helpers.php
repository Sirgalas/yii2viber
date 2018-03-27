<?php

namespace api\modules\v1\traits;

use Yii;

trait Helpers
{

    private function setHeader($status)
    {
        Yii::$app->response->statusCode = $status;
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . Yii::$app->name);
    }

    private function _getStatusCodeMessage($status)
    {
        $codes = Array(
            100 => 'Continue Server Code',
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            //Ошибка сервера
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Server Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required'
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}