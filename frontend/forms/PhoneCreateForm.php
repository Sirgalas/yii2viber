<?php
namespace frontend\forms;


/**
 * This is the class for form "phone".
 *
 * @property string $_id
 * @property int $clients_id
 * @property integer $contact_collection_id
 * @property int $phone
 * @property int $username;
 */
use yii\base\Model;

class PhoneCreateForm extends Model
{

    public $contact_collection_id;
    public $phone;
    public $username;
    public $clients_id;

    public function rules(){
        return [
            ['phone', 'required', 'message' => 'поле телефон должно быть заполнено'],
            [['phone'], 'string'],
            [['username'], 'string', 'max' => 255],
        ];
    }

}