<?php

namespace common\entities;

use common\components\Viber;
use common\entities\mongo\Message_Phone_List;
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
 * @property int $viber_image_id
 * @property string $title_button
 * @property string $url_button
 * @property string $type
 * @property string $alpha_name
 * @property int $date_start
 * @property int $date_finish
 * @property string $time_start
 * @property string $time_finish
 * @property string $status
 * @property string $message_type
 * @property string $dlr_timeout
 * @property int $limit_messages Сколько сообщений отправлять?
 * @property string $cost Стоимость
 * @property string $balance Сколько средств уже потрачено
 * @property ContactCollection $contactCollection
 * @property MessageContactCollection[] $messageContactCollections
 * @property User $user
 * @property Message_Phone_List messagePhoneList
 * @property user/User $user
 * @property 
 */
class ViberMessage extends \yii\db\ActiveRecord
{
    public $upload_file;

    public $just_now;

    public $assign_collections;

    const ONLYTEXT = 'only_text';

    const ONLYIMAGE = 'only_image';

    const TEXTBUTTON = 'txt_btn';

    const TEXTBUTTONIMAGE = 'all';

    const STATUS_NEW = 'new';

    const STATUS_READY = 'ready';

    const STATUS_WAIT = 'wait';

    const STATUS_PROCESS = 'process';

    const STATUS_SENDED = 'sended';

    public static $types = [
        self::ONLYTEXT => 'Только текст (Официально)',
        self::ONLYIMAGE => 'Только изображение (Официально)',
        self::TEXTBUTTON => 'Текст + кнопка (Официально)',
        self::TEXTBUTTONIMAGE => 'Текст + кнопка + изображение (Официально)',
    ];

    public static $status = [
        self::STATUS_NEW=>'Новое',
        self::STATUS_READY=>'Готово',
        self::STATUS_WAIT=>'Ожидает',
        self::STATUS_PROCESS=>'В процессе',
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
            [['user_id', 'limit_messages'], 'integer'],
            [ 'dlr_timeout', 'integer', 'max'=>86400 , 'min'=>0],
            [['title'], 'required'],
            [['cost', 'balance'], 'number'],
            ['viber_image_id', 'string'],
            [['title'], 'string', 'max' => 50],

            [['text'], 'string', 'max' => 1000],
            [['image', 'url_button'], 'string', 'max' => 255],
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, png, gif',
                'mimeTypes' => 'image/jpeg, image/png',
            ],
            [['title_button', 'alpha_name'], 'string', 'max' => 25],
            ['just_now', 'boolean'],
            ['type', 'in', 'range' => array_keys(static::listTypes())],
            //[['time_start', 'time_finish'], 'string', 'max' => 5],
            [['time_start', 'time_finish'], 'time', 'format' => 'php:H:i'],
            [['time_finish'], 'compare', 'compareAttribute' => 'time_start', 'operator' => '>=', 'type' => 'time'],
            ['assign_collections', 'each', 'rule' => ['integer']],
            [
                'assign_collections',
                function ($attribute, $params) {
                    $this->checkBalance($attribute, $params);
                },
            ],

            [['status'], 'string', 'max' => 16],
            ['status', 'in', 'range' => ['new', 'ready', 'wait', 'process']],
            ['message_type', 'in', 'range' => ['реклама', 'информация','Реклама', 'Информация']],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
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

            'title' => 'Название рассылки',
            'text' => 'Поле для ввода техта SMS',
            'image' => 'Изображение',
            'title_button' => 'Текст на кнопке',
            'url_button' => 'Url по кнопке',
            'type' => 'Тип сообщения',
            'alpha_name' => 'Отправитель',
            'time_start' => 'Время отправки',
            'upload_file' => 'Картинка',
            'just_now' => 'Прямо сейчас',

            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'time_finish' => 'Time Finish',
            'status' => 'Статус',
            'limit_messages' => 'Limit Messages',
            'cost' => 'Стоимость',
            'balance' => 'Баланс',
            'viber_image_id' => 'Ид изображения в Viber',
            'assign_collections' => 'Выбрать базы для рассылки',
            'dlr_timeout'=>'Время в секундах, в течение которого интересует доставка сообщения'
        ];
    }

    public function checkBalance($attribute, $params)
    {
        $oldCost = $this->cost;
        $new_cost = self::cost($this->$attribute);
        if ($new_cost - $oldCost > Yii::$app->user->identity->balance) {
            $this->addError($attribute, 'Недостаточно средств на балансе');
        }

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageContactCollections()
    {
        return $this->hasMany(MessageContactCollection::class, ['viber_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
        if ($this->just_now) {
            $this->date_start = time();
            $this->time_start = '00:00';
        }

        $this->date_finish = time() + 3600 * 24 * 1000;
        $this->time_finish = '23:59';

        if (! $this->status) {
            $this->status = self::STATUS_NEW;
        }
        if (! $this->user_id) {
            $this->user_id = Yii::$app->user->id;
        }
        if (! is_int($this->date_start)) {
            $this->date_start = strtotime($this->date_start);
        }
        if (! is_int($this->date_finish)) {
            $this->date_finish = strtotime($this->date_finish);
        }

        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub
        if ($this->hasErrors()) {
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
        if (!$this->alpha_name){
            $this->alpha_name =Yii::$app->params['viber']['from'];
        }
        if(is_a(Yii::$app,'yii\web\Application')) {

            if (is_object(Yii::$app->user)) {
                $cost = self::cost($this->assign_collections);
                if ($this->cost != $cost) {
                    Yii::$app->user->identity->balance += $this->cost - $cost;
                    $this->cost = $cost;
                    Yii::$app->user->identity->save();
                }
            }
        }
        return parent::beforeSave($insert);
    }


    public function uploadFile()
    {
        // get the uploaded file instance
        $image = UploadedFile::getInstance($this, 'upload_file');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }

        // generate random name for the file
        $this->image = time().'.'.$image->extension;

        // the uploaded image instance
        return $image;
    }

    /**
     * @return string
     */
    public function getUploadedFile()
    {
        // return a default image placeholder if your source avatar is not found
        $pic = isset($this->image) ? $this->image : 'default.png';

        return Yii::$app->params['fileUploadUrl'].$pic;
    }

    /**
     *
     */
    public function afterFind()
    {
        $this->date_start = date('Y-m-d H:i:s', $this->date_start);
        $this->date_finish = date('Y-m-d H:i:s', $this->date_finish);
        parent::afterFind();
    }

    /**
     * @return array
     */
    //public function getPhones()
    //{
    //    $tVM = ViberTransaction::find()->where(['viber_message_id' => $this->id])->andWhere(['status' => 'new'])->one();
    //}

    public static function Cost($id_collection)
    {
        if (! $id_collection) {
            $id_collection = 0;
        } else {
            foreach($id_collection as $ind=>$val){
                $id_collection[$ind] = (int)$val;
            }
        }

        $phones = Phone::find()->select(['phone'])->where(['contact_collection_id' =>   $id_collection])->column();
        if (! $phones) {
            return 0;
        }

        return count(array_unique($phones)) * Yii::$app->params['cost'];
    }

    public function userBalanse($cost = false)
    {
        if (! $cost) {
            $cost = $this->cost;
        }
        $result = $this->user->balance - $cost;

        return $result;
    }

    public function getContactCollection()
    {
        return $this->hasMany(ContactCollection::className(),
            ['id' => 'contact_collection_id'])->viaTable(MessageContactCollection::tableName(),
            ['viber_message_id' => 'id']);
    }

    public function delete()
    {
        if ($this->cost) {
            Yii::$app->user->identity->balance += 0 + $this->cost;
            Yii::$app->user->identity->save();
        }
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public function send(){
        $upload_file = $this->uploadFile();
        $transaction = Yii::$app->db->beginTransaction();
        try {

            if ($this->save()) {
                if ($upload_file !== false) {
                    $path = $this->getUploadedFile();
                    $upload_file->saveAs($path);
                }
                $result = MessageContactCollection::assign($this->id, $this->user_id, $this->assign_collections);
                if ($result !== 'ok') {
                    $this->addError('assign_collections', $result);
                    return false;
                }
            } else {
               return false;
            }
            $transaction->commit();

        } catch (\Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
            Yii::$app->session->setFlash($ex->getMessage());
            $transaction->rollBack();
            return false;
        }
        if ($this->just_now) {

            $v = new Viber($this);
            $v->prepareTransaction();
            $v->sendMessage();
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagePhoneList(){
        return $this->hasMany(Message_Phone_List::className(),['message_id'=>'id']);
    }

    public function getAlphaNames(){
        return [
            'Clickbonus'=>'Clickbonus',
            //'SALE'=>'SALE',
            //'Promo'=>'Promo',
            //'SHOP'=>'SHOP',
            //'Feedback'=>'Feedback',
            //'Sushi'=>'Sushi',
            //'Бонус'=>'Бонус',
            //'Фитнес'=>'Фитнес',
            //'Taxi'=>'Taxi',
            //'TEST'=>'TEST',
            //'ChatTest'=>'ChatTest',
            //'Dostavka'=>'Dostavka',
            //'Klinika'=>'Klinika',
            //'EXPRESS'=>'EXPRESS',
            //'Недвижимость'=>'Недвижимость',
            //'Documents'=>'Documents',
            //'AUTO'=>'AUTO'
        ];
    }

}
