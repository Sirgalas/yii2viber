<?php

namespace common\entities\mongo;
/**
 * This is the model class for table "phone".
 *
 * @property string phone
 * @property string name
 * @property int $message_id
 * @property int $transaction_id
 * @property int $last_date_message
 * @property int $status;
 * @property int $date_delivered;
 * @property int $date_viewed;
 * @property string $msg_id;
 * @property ViberMessage viberMessage
 * @property int error
 * @property int messageCount
 * @property string currency
 * @property string pricePerMessage
 */
use common\entities\ViberMessage;
use common\entities\ViberTransaction;
use yii\mongodb\ActiveRecord;


class Message_Phone_List extends ActiveRecord
{

    const NEWMESSAGE='new';
    const ERROR='err';
    const SENDED = 'sended';
    const DELIVERED = 'delivered';
    const VIEWED = 'viewed';
    const UNDELIVERED = 'undelivered';

    public static $statusMessage=
        [
            self::NEWMESSAGE=>'Новое',
            self::ERROR=>'Ошибка',
            self::SENDED=>"Отправлено",
            self::DELIVERED=>"Доставлено",
            self::VIEWED=>"Прочитано",
            self::UNDELIVERED=>'Не доставлено'
        ];
    public static $BgColor=[
        self::NEWMESSAGE=>'#3C8DBC',
        self::SENDED=>'#FFF600',
        self::DELIVERED=>'#00A65A',
        self::VIEWED=>'#F56954',
        self::UNDELIVERED=>'#222D32'
    ];

   
    public static function collectionName()
    {
        return 'message_phone_list';
    }


   

    public function attributes()
    {
        return ['_id','message_id','name','last_date_message','status','phone','date_viewed','date_delivered','transaction_id','msg_id'];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'message_id' => 'Рассылка',
            'last_date_message' => 'Дата рассылки',
            'status' => 'Статус рассылки',
            'phone'=> 'Телефоны',
            'name'=> 'Имя',
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

    public function statusMessage($status){
        return self::$statusMessage[$status];
    }
    public function BgColor($status){
        return self::$BgColor[$status];
    }
    public function allStatus():array {
        return self::$statusMessage;
    }
    public function allColor(){
        return self::$BgColor;
    }
    public function getStatusMessage(){
        return self::$statusMessage[$this->status];
    }
}