<?php

namespace common\entities\mongo;
/**
 * This is the model class for table "phone".
 *
 * @property string $_id
 * @property int $message_id
 * @property int $last_date_message
 * @property int $status;
 *
 * @property User $user
 * @property ViberMessage $currentMessage
 */

class Message_Phone_List
{

    const QUEUED = 0;
    const POSTED = 1;
    const READ = 2;

    public $_id;
    public $message_id;
    public $last_date_message;
    public $status;

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
        return ['_id','message_id','last_date_message','status','phone'];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'message_id' => 'ID рассылки',
            'last_date_message' => 'Дата рассылки',
            'status' => 'Статус рассылки',
        ];
    }

    public static function createMessagePhoneList(int $message_id,int $last_date_message, int $status){
        $messagePhoneList = new static();
        $messagePhoneList->message_id=$message_id;
        $messagePhoneList->last_date_message=$last_date_message;
        $messagePhoneList->status=$status;
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
}