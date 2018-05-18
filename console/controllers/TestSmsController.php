<?php

namespace console\controllers;

use common\entities\mongo\Message_Phone_List;
use common\entities\phone\Phone;
use common\entities\user\User;
use frontend\forms\ViberNotification;
use PHPUnit\Framework\MockObject\RuntimeException;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ContactCollection;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;
use  common\services\ViberCronHandler;
use yii\httpclient\XmlParser;

class TestSmsController extends Controller
{
    public function actionTest()
    {
        $src  = '<?xml version="1.0" encoding="utf-8"?><request><security><login value="kevmik"
/><password value="dfytccf" /></security></request>'; // XML-документ
        $href = 'http://lk.prontosms.ru/xml/'; // адрес сервера
        $res  = '';
        $ch   = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: text/xml; charset=utf-8']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
        curl_setopt($ch, CURLOPT_URL, $href);
        $result = curl_exec($ch);
        $res    = $result;
        curl_close($ch);
        echo '---------', $res, '=========';
    }

    public function actionSend()
    {
        $src = '<?xml version="1.0" encoding="utf-8" ?><request>
<message type_send_1="sms" type_send_2="sms" type="sms">
<sender>79135701037</sender>
<text>Текст сообщени-8</text> 
 
<abonent phone="79135862350" number_sms="7" client_id_sms="107" />   


</message> 
<security>
<login value="FinCap" />
<password value="92dS4n" />
</security>
</request>
';

        $src  = str_replace("\n", '', $src);
        $src  = str_replace("\r", '', $src);
        $href = 'http://lk.prontosms.ru/xml/'; // адрес сервера
        $res  = '';
        $ch   = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: text/xml; charset=utf-8']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
        curl_setopt($ch, CURLOPT_URL, $href);
        $result = curl_exec($ch);
        $res    = $result;
        curl_close($ch);
        echo '---------', $res, '=========';
    }
}
