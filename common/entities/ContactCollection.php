<?php

namespace common\entities;


use common\entities\mongo\Phone;
//use common\entities\Phone;
use common\entities\user\User;
use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "contact_collection".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $type
 * @property int $created_at
 *
 * @property User $user
 * @property MessageContactCollection[] $messageContactCollections
 * @property Phone[] $phones
 * @property ViberMessage $viberMessage
 */
class ContactCollection extends \yii\db\ActiveRecord
{

    public $extension;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_collection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'default', 'value' => null],
            [['user_id', 'created_at'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 10],
            ['type', 'in', 'range'=>static::listTypes()],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Клиент',
            'title' => 'Название Коллекции',
            'type' => 'Type',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ContactCollectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContactCollectionQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhones(){
        return $this->hasMany(Phone::class(),['contact_collection_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageContactCollections()
    {
        return $this->hasMany(MessageContactCollection::class(), ['contact_collection_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function listTypes(){
        return ['viber', 'sms'];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (!$this->user_id){
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public function fileExel(){
        return ['xls','xlsx'];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!$this->user_id){
            $this->user_id = Yii::$app->user->id;
        }
        if ($insert){
            $this->created_at = time();
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function phoneSave($post,$id_collection){
        $phoneId = Yii::$app->request->post('editableKey');
        $model = Phone::findOne($phoneId);
        $out = Json::encode(['output'=>'', 'message'=>'']);
        $posted = current($_POST['Phone']);
        $key=key($posted);
        $value=ArrayHelper::getValue($posted,$key);
        $model->$key=$value;
        $model->save();
        $output = '';
        $out = Json::encode(['output'=>$output, 'message'=>'']);
        echo $out;
        return ;
    }

    public function getExtension(){
        return $this->extension;
    }

    public function getViberMessage(){
        return $this->hasOne(ViberMessage::class(), ['id' =>'viber_message_id'])->viaTable(MessageContactCollection::tableName(), [ 'contact_collection_id'=>'id']);
    }

}
