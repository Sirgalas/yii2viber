<?php

namespace common\entities;


use common\entities\user\User;
use Yii;
use yii\db\Exception;
use yii\web\UploadedFile;
use common\entities\mongo\Phone;
/**

 * This is the model class for table "viber_message".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $text
 * @property string $image
 * @property string $title_button
 * @property string $url_button
 * @property string $type
 * @property string $alpha_name
 * @property int $date_start
 * @property int $date_finish
 * @property string $time_start
 * @property string $time_finish
 * @property string $status
 * @property int $limit_messages Сколько сообщений отправлять?
 * @property string $cost Стоимость
 * @property string $balance Сколько средств уже потрачено
 * @property ContactCollection $contactCollection
 * @property MessageContactCollection[] $messageContactCollections
 *
 * @property user/User $user
 */
class ViberMessage extends \yii\db\ActiveRecord
{
    public $upload_file;
    const ONLYTEXT ='only_text';
    const ONLYIMAGE ='only_image';
    const TEXTBUTTON ='txt_btn';
    const TEXTBUTTONIMAGE ='all';


    const STATUS_NEW = 'new';
    const STATUS_READY = 'ready';
    const STATUS_WAIT = 'wait';
    const STATUS_PROCESS ='process';

    public static $types=[
        self::ONLYTEXT          => 'Только текст (Официально)',
        self::ONLYIMAGE         => 'Только изображение (Официально)',
        self::TEXTBUTTON        => 'Текст + кнопка (Официально)',
        self::TEXTBUTTONIMAGE   => 'Текст + кнопка + изображение (Официально)',
        ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'viber_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date_start', 'date_finish', 'limit_messages'], 'default', 'value' => null],
            [['user_id',   'limit_messages'], 'integer'],
            [['title'], 'required'],
            [['cost', 'balance'], 'number'],
            [['title'], 'string', 'max' => 50],
            [['text'], 'string', 'max' => 120],
            [['image', 'url_button'], 'string', 'max' => 255],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png, gif', 'mimeTypes' => 'image/jpeg, image/png',],
            [['title_button', 'alpha_name'], 'string', 'max' => 32],

            ['type', 'in', 'range' => array_keys(static::listTypes())],
            //[['time_start', 'time_finish'], 'string', 'max' => 5],
            [['time_start', 'time_finish'], 'time', 'format' => 'php:H:i'],
            [['status'], 'string', 'max' => 16],
            ['status', 'in', 'range'=>['new','ready','wait', 'process' ]],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'text' => 'Text',
            'image' => 'Image',
            'title_button' => 'Title Button',
            'url_button' => 'Url Button',
            'type' => 'Type',
            'alpha_name' => 'Alpha Name',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'time_start' => 'Time Start',
            'time_finish' => 'Time Finish',
            'status' => 'Status',
            'limit_messages' => 'Limit Messages',
            'cost' => 'Cost',
            'balance' => 'Balance',
            'upload_file' => 'Upload File',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageContactCollections()
    {
        return $this->hasMany(MessageContactCollection::className(), ['viber_message_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }



    /**
     * @inheritdoc
     * @return ViberMessageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ViberMessageQuery(get_called_class());
    }

    public static function listTypes()
    {
        return self::$types;
    }

    public static function getTypeText($ind)
    {
       return self::$types[$ind];
    }

    public static function getTypeComment($ind)
    {

        return self::$types[$ind];
    }

    public function beforeValidate()
    {
        if (!$this->status){
            $this->status = self::STATUS_NEW;
        }
        if (!$this->user_id){
            $this->user_id = Yii::$app->user->id;
        }
        if (!is_int($this->date_start)){
            $this->date_start=strtotime($this->date_start);
        }
        if (!is_int($this->date_finish)){
            $this->date_finish=strtotime($this->date_finish);
        }
        return parent::beforeValidate();
    }

     public function afterValidate()
     {
         parent::afterValidate(); // TODO: Change the autogenerated stub
         if ($this->hasErrors()){
             if (is_int($this->date_start)) {
                 $this->date_start = date('Y-m-d H:i:s', $this->date_start);
             }
             if (is_int($this->date_finish)) {
                 $this->date_finish = date('Y-m-d H:i:s', $this->date_finish);
             }
         }
     }

     public function beforeSave($insert)
     {
         if($insert){
             $this->cost=0;
         }else{
             $arrId=[];
             foreach ($this->contactCollection as $id){
                 $arrId=$id->id;
             }
             $this->cost=$this->Coast($arrId);
         }
         return parent::beforeSave($insert); // TODO: Change the autogenerated stub
     }

    public function uploadFile() {
        // get the uploaded file instance
        $image = UploadedFile::getInstance($this, 'upload_file');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }

        // generate random name for the file
        $this->image = time(). '.' . $image->extension;

        // the uploaded image instance
        return $image;
    }

    /**
     * @return string
     */
    public function getUploadedFile() {
        // return a default image placeholder if your source avatar is not found
        $pic = isset($this->image) ? $this->image : 'default.png';
        return Yii::$app->params['fileUploadUrl'] . $pic;
    }

    /**
     *
     */
    public function afterFind()
    {
        $this->date_start = date('Y-m-d H:i:s', $this->date_start);
        $this->date_finish = date('Y-m-d H:i:s', $this->date_finish);
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array
     */
    public function getPhones(){
        return ['79135701037',
            //'79050885202'
            '79663396630'
        ];
    }

    public function Coast($id_collection){
        if(!$id_collection)
            $id_collection=0;
        $phones=Phone::find()->select(['phone'])->where(['contact_collection_id'=>$id_collection])->column();
        if(!$phones)
            return 0;
        return count(array_unique($phones))*Yii::$app->params['coast'];
    }

    public function getContactCollection(){
        return $this->hasOne(ContactCollection::className(), ['id' => 'contact_collection_id'])->viaTable(MessageContactCollection::tableName(), ['viber_message_id' => 'id']);
    }
}
