<?php

namespace common\entities\mongo;
/**
 * This is the model class for table "phone".
 *
 * @property string phone
 * @property int $message_id
 * @property int $transaction_id
 * @property int $last_date_message
 * @property int $status;
 * @property int $date_delivered;
 * @property int $date_viewed;
 * @property string $msg_id;
 * @property ViberMessage viberMessage
 * @property ViberTransaction viberTransaction
 */
use common\entities\ViberMessage;
use common\entities\ViberTransaction;
use yii\mongodb\ActiveRecord;


class Message_Phone_List extends ActiveRecord
{

    const NEWMESSAGE='new';
    const SENDED = 'sended';
    const DELIVERED = 'delivered';
    const VIEWED = 'viewed';


    public static $statusMessage=
        [
            self::NEWMESSAGE=>"Новое",
            self::SENDED=>"Отправлено",
            self::DELIVERED=>"Получено",
            self::VIEWED=>"Прочитано",
        ];

    public static function collectionName()
    {
        return 'message_phone_list';
    }
    
    public function getStatusMessage(){
        return $this::$statusMessage[$this->status];
    }

    public function attributes()
    {
        return ['_id','message_id','last_date_message','status','phone','date_viewed','date_delivered','transaction_id','msg_id'];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'message_id' => 'Рассылка',
            'last_date_message' => 'Дата рассылки',
            'status' => 'Статус рассылки',
            'phone'=> 'Телефоны',
            'date_viewed'=>'Дата просмотра',
            'date_delivered'=>'Дата доставки',
            'transaction_id'=> 'Транзанкция',
            'msg_id'=>'id сообщения в вайбере'
        ];
    }

    public function getStatus(){
        return self::$statusMessage[$this->status];
    }

    public function isNew(){
        return $this->status==self::NEWMESSAGE;
    }
    public function isSended(){
        return $this->status==self::SENDED;
    }
    public function isDelivered(){
        return $this->status==self::DELIVERED;
    }
    public function isViewed(){
        return $this->status=self::VIEWED;
    }
    public function getViberMessage(){
        return $this->hasOne(ViberMessage::class,['id'=>'message_id']);
    }
    public function getViberTransaction(){
        return $this->hasOne(ViberTransaction::class,['id'=>'transaction_id']);
    }
}