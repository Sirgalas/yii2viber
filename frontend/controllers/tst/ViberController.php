<?php

namespace backend\controllers\tst;

use yii\helpers\Url;
use Yii;

class ViberController extends \yii\web\Controller
{
    private function getPath($transaction_id)
    {

        $path = Yii::getAlias('@runtime/log/tst');
        if (! file_exists($path) && mkdir($path, 0755, true)) {
            throw new \Exception('Нет каталога '.$path);
        }

        return $path.'/'.$transaction_id.'.log';
    }

    public function actionIndex()
    {
        if (count($_POST) == 0) {
            $path=$this->getPath();
            $dir = opendir($path);
            $list = [];
            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..' && $file[strlen($file) - 1] != '~') {
                    $ctime = filectime($path.$file).','.$file;
                    $list[$ctime] = $file;
                }
            }
            closedir($dir);
            krsort($list); // используя методы krsort и ksort можем влиять на порядок сортировки

            foreach ($list as $f){
                echo $f, '<br>';
            };
        } else {
            $phones = $_POST['phone'];
            $transaction_id = $_POST['p_transaction_id'];
            $xml = '
<?xml version="1.0"?>
<response>
    <tech_message>OK</tech_message>
    <code>0</code>
    ';

            $msgids = [];
            foreach ($phones as $phone) {
                $msg_id = date('Ymd-H-i-s---').$phone;
                $msgids[] = $msg_id;
                $xml .= '<msg_id phone="'.$phone.'">'.$msg_id."</msg_id>\n";
            }

            $xml .= '</response>';
            file_put_contents($this->getPath($transaction_id), implode(';', $phones));

            return $xml;
        }
    }

    public function sendNotification($id)
    {
        $msgids = explode(';', file_get_contents($this->getPath($id)));
        $url = Yii::$app->params['frontendHostInfo'].'/viber/report';
        foreach ($msgids as $msg_id) {
            $params = [
                'sending_method' => 'viber',
                'type' => 'delivery',
                'status' => 'delivered',
                'p_transaction_id' => $id,
                'msg_id' => $msg_id,
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
