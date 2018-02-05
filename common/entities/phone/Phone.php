<?php
namespace common\entities\phone;

use common\entities\ContactCollection;
use yii\mongodb\ActiveRecord;
use yii\web\User;
use common\entities\ViberMessage;

/**
 * This is the model class for table "phone".
 *
 * @property string $_id
 * @property int $clients_id
 * @property integer $contact_collection_id
 * @property int $phone
 * @property int $username;
 *
 * @property User $user
 * @property ViberMessage $currentMessage
 */

class Phone extends ActiveRecord
{
    public $contact_collection_id;
    public $phone;
    public $clients_id;
    public $username;

    /**
     * @param int $contact_collection_id
     * @param int $phone
     * @param int $clients_id
     * @param string $username
     */
    public static function createPhone(int $contact_collection_id,int $phone, int $clients_id,string $username){
        $phones = new static();
        $phones->contact_collection_id=$contact_collection_id;
        $phones->phone=$phone;
        $phones->clients_id=$clients_id;
        $phones->username=$username;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return ['_id','contact_collection_id','phone','clients_id','username'];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'clients_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getContactCollection(){
        return $this->hasOne(ContactCollection::className(),['id'=>'contact_collection_id']);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'contact_collection_id' => 'ID коллекции контактов',
            'clients_id' => 'ID клиента собственника базы',
            'username' => 'Имя владельца номера',
        ];
    }

}