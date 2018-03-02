<?php
namespace common\modules\api\traits;
use Yii;
use yii\filters\auth\HttpBearerAuth;
trait Aplication{
    public function behaviors()
    {
        /* $behaviors = parent::behaviors();
         Чтобы пользоваться нашим API посредством ajax-запросов с других доменов
         $behaviors['corsFilter'] = [
             'class' => \yii\filters\Cors::className(),
         ];*/
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        //Используем только JSON формат
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }
    /**
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        //unset($actions['options']);
        return $actions;
    }


    protected function sendError($error)
    {
        $this->setHeader(400);
        return array('status' => 0, 'error_code' => 400, 'errors' => $error);
    }
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