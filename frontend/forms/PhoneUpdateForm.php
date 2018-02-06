<?php
namespace frontend\forms;

use common\entities\mongo\Phone;
use yii\base\Model;

class PhoneUpdateForm extends Model
{
    public $contact_collection_id;
    public $phone;
    public $username;
    public $clients_id;
    public function __construct(Phone $phone,array $config = []){
        $this->contact_collection_id=$phone->contact_collection_id;
        $this->phone=$phone->phone;
        $this->username=$phone->username;
        $this->clients_id=$phone->clients_id;
        parent::__construct($config);
    }

    public function rules(){
        return [
            ['phone', 'required', 'message' => 'поле телефон должно быть заполнено'],
            [['phone'], 'string'],
            [['username'], 'string', 'max' => 255],
        ];
    }
}