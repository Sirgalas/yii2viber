<?php

namespace common\entities;

use common\components\Viber;
use common\entities\mongo\Message_Phone_List;
use common\entities\user\User;
use PHPUnit\Framework\MockObject\RuntimeException;
use Yii;
use yii\db\Exception;
use yii\helpers\FileHelper;

use common\entities\mongo\Phone;
use Friday14\Mailru\Cloud;

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
 * @property int date_send_finish время окончания рассылки
 * @property string provider ид сценария, который будет использоваться для рассылки
 * @property int scenario_id ид сценария, который будет использоваться для рассылки
 *
 * @property ContactCollection $contactCollection
 * @property MessageContactCollection[] $messageContactCollections
 * @property User $user
 * @property Message_Phone_List messagePhoneList
 * @property user/User $user
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

    const SCENARIO_DEFAULT = 'default';

    const SCENARIO_HARD = 'hard';

    //new:check:checked:process:wait:ready:error:cancel:closed
    //renew
    //
    const STATUS_PRE = 'pre';

    const STATUS_FIX = 'fix';

    const STATUS_CHECK = 'check';

    const STATUS_NEW = 'new';

    const STATUS_WAIT = 'wait';

    const  STATUS_WAIT_PAY = 'wait-pay';

    const STATUS_PROCESS = 'process';

    const STATUS_READY = 'ready';

    const STATUS_ERROR = 'error';

    const STATUS_CLOSED = 'closed';

    const STATUS_CANCEL = 'cancel';

    /**
     *
     *   Таблица переходов
     *  1. Клиент создает STATUS_PRE
     *  2. Клиент отправляет на модерацию STATUS_PRE -> STATUS_CHECK
     *  3. Админ проверяет и если все ОК STATUS_CHECK->STATUS_NEW
     *                         иначе STATUS_CHECK->STATUS_FIX
     * 4. Cron Handler - готовит транзакции и STATUS_NEW->STATUS_PROCESS
     * 5. Cron handler отправляет все транзакции и STATUS_PROCESS->STATUS_WAIT
     * 6. Cron handler проверяет все ли доставлено или остек срок срок доставки STATUS_WAIT->STATUS_READY
     * 7. Если при отправке произошла ошибка STATUS_PROCESS->STATUS_ERROR
     * 8. Если приотправке не хватило средств на балансе, то STATUS_PROCESS->STATUS_WAIT_PAY
     * 9. Cron handler проверяет не появились ли новые средства на счете и если появились STATUS_WAIT_PAY->STATUS_PROCESS
     * 10. Админ может закрыть любую расылку в любой момент *****->STATUS_CLOSED
     * 11.  Клиент может остановить рассылку в процессе обработки STATUS_CHECK->STATUS_CANCEL, STATUS_PROCESS->STATUS_CANCEL
     *
     */
    public static $types = [
        self::ONLYTEXT => 'Только текст (Официально)',
        self::ONLYIMAGE => 'Только изображение (Официально)',
        self::TEXTBUTTON => 'Текст + кнопка (Официально)',
        self::TEXTBUTTONIMAGE => 'Текст + кнопка + изображение (Официально)',
    ];

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['hard'] = $scenarios['default'];//Scenario Values Only Accepted

        return $scenarios;
    }

    /**
     * @return bool
     */
    public function isDeleteble()
    {
        return $this->status == ViberMessage::STATUS_PRE || $this->status == ViberMessage::STATUS_FIX || $this->status == ViberMessage::STATUS_CHECK;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->status == ViberMessage::STATUS_PRE || $this->status == ViberMessage::STATUS_FIX;
    }

    public function isCancalable()
    {
        return $this->status == ViberMessage::STATUS_CHECK || ViberMessage::STATUS_NEW || $this->status == ViberMessage::STATUS_PROCESS;
    }

    public static $status = [
        self::STATUS_PRE => 'Новое',
        self::STATUS_FIX => 'Исправл.',
        self::STATUS_CHECK => 'Модер.',
        self::STATUS_CANCEL => 'Прерв.',
        self::STATUS_CLOSED => 'Закрыто.',
        self::STATUS_ERROR => 'Ошибка',
        self::STATUS_NEW => 'Утвержд.',
        self::STATUS_READY => 'Готово',
        self::STATUS_WAIT => 'Ожидает',
        self::STATUS_PROCESS => 'В процессе',
        self::STATUS_WAIT_PAY => 'Ожидание платежа',
    ];

    public function SetWaitPay(){
        throw new Exception('Не готово!!!!');
        if ($this->status == self::STATUS_PROCESS){
            $this->status = self::STATUS_WAIT_PAY;
            $cnt = ViberTransaction::find()->where(['viber_message_id'=>$this->id])->andWhere(['status'=>ViberTransaction::NEWSEND])->sum('size');
            $this->wait_payment_comment('не хватило средств для отправки ' . $cnt. ' сообщений');
            return $this->save();
        }
        return false;
    }

    public function SetProcess(){
        if ($this->status == self::STATUS_WAIT_PAY || $this->status == self::STATUS_NEW){
            $this->status = self::STATUS_PROCESS;
            return $this->save();
        }
        return false;
    }

    public function Cancel()
    {
        if ($this->isCancalable()) {
            $this->status = self::STATUS_CANCEL;

            return $this->save();
        }

        return false;
    }

    public function Close()
    {
        if (Yii::$app->user->identity->isAdmin()) {
            $this->status = self::STATUS_CLOSED;

            return $this->save();
        }

        return false;
    }

    public function isPromotional()
    {
        return (strtolower($this->message_type) !== 'информация');
    }

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
            [['user_id', 'limit_messages', 'scenario_id'], 'integer'],
            ['dlr_timeout', 'integer', 'max' => 86400, 'min' => 0],
            [['title'], 'required'],
            [['cost', 'balance'], 'number'],
            ['viber_image_id', 'string'],
            [['title'], 'string', 'max' => 50, 'min' => 3],

            [['text'], 'string', 'max' => 1000],
            [
                'text',
                'required',
                'when' => function ($model) {
                    return $model->scenario === 'hard' && $model->type != self::ONLYIMAGE;
                },
            ],
            [['image', 'url_button'], 'string', 'max' => 255],
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, png, gif',
                'mimeTypes' => 'image/jpeg, image/png',
                'maxSize' => 400 * 1024,
            ],
            [
                'image',
                'required',
                'when' => function ($model) {
                    return ($model->scenario === 'hard'
                        && ($model->type == self::ONLYIMAGE || $model->type == self::TEXTBUTTONIMAGE));
                },
            ],

            [
                ['title_button', 'url_button'],
                'required',
                'when' => function ($model) {
                    return ($model->scenario === 'hard'
                        && ($model->type == self::TEXTBUTTON || $model->type == self::TEXTBUTTONIMAGE)
                    );
                },
            ],
            [['title_button'], 'string', 'max' => 20],
            [['alpha_name', 'provider'], 'string', 'max' => 25],
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
            ['assign_collections', 'required', 'on' => ['hard']],

            [['status'], 'string', 'max' => 16],
            ['status', 'in', 'range' => ['pre', 'fix', 'check', 'closed', 'cancel', 'new', 'ready', 'wait', 'process']],

            ['message_type', 'in', 'range' => ['реклама', 'информация', 'Реклама', 'Информация']],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
            [['admin_comment','wait_payment_comment'], 'string', 'max'=>255],
            [['provider'], 'string', 'max'=>16],
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
            'dlr_timeout' => 'Время в секундах, в течение которого интересует доставка сообщения',
            'wait_payment_comment'=> 'коммент о недостатке баланса',
            'admin_comment'=>'коммент администратора',
            'provider'=> 'провайдер'
        ];
    }

    public function checkBalance($attribute, $params)
    {
        $oldCost = $this->cost;
        $new_cost = self::cost($this->$attribute);
        if ($new_cost - $oldCost > Yii::$app->user->identity->balance) {
            $this->addError($attribute, 'Недостаточно средств на балансе');
        }

        if ($this->scenario == ViberMessage::SCENARIO_HARD && $new_cost < 1) {
            $this->addError($attribute, 'Нет телефонов в рассылке');
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
        $this->viber_image_id = (string)$this->viber_image_id;
        $this->date_finish = time() + 3600 * 24 * 1000;
        $this->time_finish = '23:59';

        if (! $this->status) {
            $this->status = self::STATUS_PRE;
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

        $this->defineProvider();

        return parent::beforeValidate();
    }

    public function defineProvider()
    {
        if ($this->provider) {
            return $this->provider;
        }
        if ($this->id) {
            $this->provider = 'smsonline';
        } else {
            $this->provider = 'infobip';
        }

        return $this->provider;
    }

    /**
     *
     */
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

    /**
     * Перевод транзакции в состояние Wait
     */
    public function setWait()
    {
        $this->status = self::STATUS_WAIT;
        $this->date_send_finish = time();
        return $this->save();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->dlr_timeout) {
            $this->dlr_timeout = 24 * 3600;
        }
        if (! $this->alpha_name) {
            $this->alpha_name = Yii::$app->params['smsonline']['from'];
        }
        if (is_a(Yii::$app, 'yii\web\Application')) {

            if (is_object(Yii::$app->user) && $this->assign_collections) {
                $cost = self::cost($this->assign_collections);
                if ($this->cost != $cost) {
                    //Yii::$app->user->identity->balance += $this->cost - $cost;
                    $this->cost = $cost;
                    Yii::$app->user->identity->save();
                }
            }
        }

        return parent::beforeSave($insert);
    }

    public function uploadFile()
    {
        if (empty($this->upload_file)) {
            return false;
        }
        $imageName=time().'.'.$this->upload_file->extension;
        $filepath='image/'.date('m_Y',time()).'/'.Yii::$app->user->identity->username;
        FileHelper::createDirectory($filepath,0777);
        if(!$this->upload_file->saveAs($filepath.'/'.$imageName))
            throw new \RuntimeException('ошибка загрузки файла');
        $cloud = new \Friday14\Mailru\Cloud(Yii::$app->params['cloud'], Yii::$app->params['cloudpass'], 'mail.ru');
        $file = new \SplFileObject($filepath.'/'.$imageName,"r");
        $cloud->upload($file,$filepath.'/'.$imageName);
        $cloudImageLink=$cloud->getLink($filepath.'/'.$imageName);
        @unlink(\Yii::getAlias('@frontend').'/web/' . $filepath.'/'.$imageName);
        $this->upload_file=null;
        return $cloudImageLink;
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
            foreach ($id_collection as $ind => $val) {
                $id_collection[$ind] = (int)$val;
            }
        }

        $phones = Phone::find()->select(['phone'])->where(['contact_collection_id' => $id_collection])->column();
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

     public function send()
    {
        $upload_file = $this->uploadFile();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($upload_file) {
                $this->image = $upload_file;
            }
            if ($this->save()) {
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
        if ($this->just_now && $this->status == self::STATUS_NEW) {
            $v = new Viber($this);
            $v->prepareTransaction();
            $v->sendMessage();
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagePhoneList()
    {
        return $this->hasMany(Message_Phone_List::className(), ['message_id' => 'id']);
    }

    public function getAlphaNamesOptions()
    {
        return [
            //'Clickbonus'=>'Бонус',
            //'SALE'=>'SALE',

            //'Promo'=>['disabled'=>true],
            //'Feedback'=>['disabled'=>true],
            //
            //'Бонус'=>['disabled'=>true],
            //'Фитнес'=>['disabled'=>true],
            //'Taxi'=>['disabled'=>true],
            //'TEST'=>['disabled'=>true],
            //'ChatTest'=>['disabled'=>true],
            //'Dostavka'=>['disabled'=>true],
            //'Klinika'=>['disabled'=>true],
            //'EXPRESS'=>['disabled'=>true],
            //'Недвижимость'=>['disabled'=>true],
            //'Documents'=>['disabled'=>true],
            //'AUTO'=>['disabled'=>true],
        ];
    }
}
