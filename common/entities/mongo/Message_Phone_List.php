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

    const QUEUED = 0;
    const POSTED = 1;
    const READ = 2;


    public static $statusMessage=
        [
            self::QUEUED=>"Отправлено",
            self::POSTED=>"Получено",
            self::READ=>"Прочитано",
        ];

    public static function collectionName()
    {
        return 'message_phone_list';
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

    public function isQueued(){
        return $this->status==self::QUEUED;
    }
    public function isPosted(){
        return $this->status==self::POSTED;
    }
    public function isRead(){
        return $this->status==self::READ;
    }
    public function getViberMessage(){
        return $this->hasMany(ViberMessage::className(),['id'=>'message_id']);
    }
    public function getViberTransaction(){
        return $this->hasMany(ViberTransaction::className(),['id'=>'transaction_id']);
    }
}