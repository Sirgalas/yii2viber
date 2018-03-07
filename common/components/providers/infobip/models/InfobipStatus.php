<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 07.03.2018
 * Time: 0:40
 */

namespace common\components\providers\infobip\models;

use common\entities\mongo\Message_Phone_List;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;

class InfobipStatus
{


    public static function parseStatus($message = '')
    {
        try {
            if (! $message) {
                return Message_Phone_List::UNDELIVERED;
            }
            $status = $message['status'];

            if ($status['groupId'] === 1) {
                return  Message_Phone_List::SENDED;
            }
            if ($status['groupId'] === 2) {
                return  Message_Phone_List::UNDELIVERED;
            }
            if ($status['groupId'] === 3) {
                if ($status['id'] === 2) {
                    return  Message_Phone_List::DELIVERED;
                } else {
                    return Message_Phone_List::VIEWED;
                }
            }
            if ($status['groupId'] === 4) {
                return  Message_Phone_List::UNDELIVERED;
            }
            if ($status['groupId'] === 5) {
                Yii::warning('message REJECTED:: ' . print_r($message,1));
            return  Message_Phone_List::UNDELIVERED;
                }
        } catch (\RuntimeException $e) {
            Yii::error('parseStatus Message ::' .  print_r($message,1));
            return  Message_Phone_List::UNDELIVERED;
        }
    }
}