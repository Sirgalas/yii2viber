<?php

namespace frontend\controllers\tst;

use yii\helpers\Url;
use Yii;

class ViberController extends \yii\web\Controller
{
    private function getPath($transaction_id = 0)
    {

        $path = Yii::getAlias('@runtime/log/tst');
        if (! file_exists($path) && ! mkdir($path, 0755, true)) {
            throw new \Exception('Нет каталога '.$path);
        }
        if ($transaction_id) {
            return $path.'/'.$transaction_id.'.log';
        } else {
            return $path;
        }
    }

    public function beforeAction($action)
    {

        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionIndex($f = '')
    {
        if (count($_POST) == 0) {
            $path = $this->getPath();
            $dir = opendir($path);
            $list = [];
            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..' && $file[strlen($file) - 1] != '~') {

                    $list[$file] = $file;
                }
            }
            closedir($dir);
            krsort($list); // используя методы krsort и ksort можем влиять на порядок сортировки
            echo '<h1>Логи</h1><hr>';
            foreach ($list as $f) {
                echo "<a href=\"/tst/viber/notification?id=$f\" target='_blank'>$f</a>", '   ', filesize($path.'/'.$f), '<br>';
            };
            exit;
        } else {
            $rpost = explode('&', file_get_contents('php://input'));
            $post = [];
            $phones = [];
            foreach ($rpost as $item) {
                list($key, $val) = explode('=', $item);
                if ($key == 'phone') {
                    $phones[] = $val;
                }
                $post[$key] = $val;
            }
            $transaction_id = $_POST['p_transaction_id'];
            $xml = '<?xml version="1.0"?>
<response>
    <tech_message>OK</tech_message>
    <code>0</code>
    ';

            $msgids = [];
            if (! is_array($phones)) {

                $phones = [$phones];
            }

            foreach ($phones as $phone) {

                $msg_id = date('Ymd-H-i-s---').$phone;
                $msgids[] = $msg_id;
                $xml .= '<msg_id phone="'.$phone.'">'.$msg_id."</msg_id>\n";
            }

            $xml .= '</response>';
            file_put_contents($this->getPath($transaction_id), implode(';', $msgids));

            return $xml;
        }
    }

    private function multi($url, $data, $options = [])
    {
        $mh = curl_multi_init(); // init the curl Multi

        $aCurlHandles = []; // create an array for the individual curl handles

        foreach ($data as $id => $d) {

            $url = (is_array($d) && ! empty($d['url'])) ? $d['url'] : $d;

            $ch = curl_init(); // init curl, and then setup your options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // returns the result - very important
            curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
            // Если указали дополнительные параметры $options то устанавливаем их
            // смотри документацию функции curl_setopt_array
            if (count($options) > 0) {
                curl_setopt_array($ch, $options);
            }

            $aCurlHandles[$id] = $ch;
            curl_multi_add_handle($mh, $ch);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) == -1) {
                usleep(20);
            }
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
        foreach ($aCurlHandles as $id => $ch) {
            $html[$id] = curl_multi_getcontent($ch); // get the content
            // do what you want with the HTML
            curl_multi_remove_handle($mh, $ch); // remove the handle (assuming  you are done with it)
            //curl_close($ch);
        }
        curl_multi_close($mh); // close the curl multi handler

        return $html;
    }

    public function actionNotification($id = '')
    {
        $msgids = explode(';', file_get_contents($this->getPath().'/'.$id));
        $url = Yii::$app->params['frontendHostInfo'].'/viber/report';
        list($transaction_id, $lst) = explode('.', $id);

        $startTime = time();
        for ($j = 0; $j < 10; $j++) {

            $mh = curl_multi_init(); // init the curl Multi

            $aCurlHandles = []; // create an array for the individual curl handles
            for ($i = 0; $i < 15; $i++) {

                foreach ($msgids as $msg_id) {
                    $params = [
                        'sending_method' => 'viber',
                        'type' => 'delivery',
                        'status' => 'delivered',
                        'p_transaction_id' => $transaction_id,
                        'msg_id' => $msg_id,
                    ];
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    $aCurlHandles[$i.'-'.$j.'-'.$msg_id] = $ch;
                    curl_multi_add_handle($mh, $ch);
                }
            }

            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) == -1) {
                    usleep(20);
                }
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
            echo "<h1> from $startTime To ".time().' = '.(time() - $startTime).'</h1>';
            foreach ($aCurlHandles as $id => $ch) {
                echo '<br>', $id, ' ';
                print_r(curl_multi_getcontent($ch));
                $html[$id] = curl_multi_getcontent($ch); // get the content
                // do what you want with the HTML
                curl_multi_remove_handle($mh, $ch); // remove the handle (assuming  you are done with it)
                //curl_close($ch);
            }
            curl_multi_close($mh); // close the curl multi handler
        }
        exit;
    }
}
