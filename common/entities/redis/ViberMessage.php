<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.02.18
 * Time: 19:06
 */

namespace common\entities\redis;


use common\entities\mongo\Message_Phone_List;
use common\entities\ViberTransaction;

class ViberMessage extends \yii\redis\ActiveRecord
{
    public function attributes()
    {
        return ['msg_id', 'p_transaction_id', 'type', 'status','sending_method','phone'];
    }

    public function getMessagePhoneLIst(){
        return $this->hasOne(Message_Phone_List::class,['msg_id'=>'msg_id']);
    }

    public function getTransaction(){
        return $this->hasOne(ViberTransaction::class,['id'=>'p_transaction_id']);
    }
}