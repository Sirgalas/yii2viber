<?php

namespace common\entities;

use common\entities\mongo\Message_Phone_List;
use frontend\forms\ViberNotification;
use Yii;
use common\entities\user\User;

/**
 * This is the model class for table "viber_transaction".
 *
 * @property int $id
 * @property int $user_id
 * @property int $viber_message_id
 * @property string $status
 * @property int $created_at
 * @property int $date_send
 * @property int $delivered
 * @property int $size
 * @property int $viewed
 * @property string $phones
 *
 * @property User $user
 * @property ViberMessage $viberMessage
 * @property Message_Phone_List messagePhoneList
 */
class ViberTransaction extends \yii\db\ActiveRecord
{

    public $titleSearch;
    public $contactCollection;
    public $dateFrom;
    public $dateTo;

    const NEWSEND='new';
    const SENDED='sended';
    const DELIVERED='delevired';
    const  VIEWED='viewed';

    public static $statusSend = [
        self::NEWSEND=>'Новое',
        self::SENDED=>'Отправленно',
        self::DELIVERED=>'Доставлено',
        self::VIEWED=>'Просмотрено',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'viber_transaction';
    }

    const SCENARIO_SEARCH = 'search';
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = [];
        return $scenarios;
    }
        //new =>new
        //
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'viber_message_id', 'status', 'created_at'], 'required'],
            [['user_id', 'viber_message_id', 'created_at', 'delivered', 'viewed'], 'default', 'value' => null],
            [['user_id', 'viber_message_id', 'size', 'created_at','date_send', 'delivered', 'viewed'], 'integer'],
            [['phones'], 'string'],
            [['status'], 'string', 'max' => 60],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
            [
                ['viber_message_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ViberMessage::className(),
                'targetAttribute' => ['viber_message_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'viber_message_id' => Yii::t('app', 'Viber Message ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'delivered' => Yii::t('app', 'Delivered'),
            'viewed' => Yii::t('app', 'Viewed'),
            'phones' => Yii::t('app', 'Phones'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViberMessage()
    {
        return $this->hasOne(ViberMessage::className(), ['id' => 'viber_message_id']);
    }

    /**
     * @inheritdoc
     * @return ViberTransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ViberTransactionQuery(get_called_class());
    }

    public function getPhonesArray()
    {
        return (array)\GuzzleHttp\json_decode($this->phones,true);
    }

    public function handleViberNotification(ViberNotification $vb_Note)
    {
        $phonesArray = $this->getPhonesArray();
        $changed = false;
        print_r($phonesArray) ;
        if (array_key_exists($vb_Note->msg_id, $phonesArray)) {
            $phone = $phonesArray[$phonesArray[$vb_Note->msg_id]];
            $changed = false;
            if ($phone['status'] === 'new' || $phone['status'] === 'sended') {
                if ($vb_Note->type == 'delivered') {
                    $this->delivered += 1;
                    $phone['status'] = 'delivered';
                    $phone['date_delivered'] =time();
                    $changed = true;
                }
                if ($vb_Note->type == 'seen') {
                    $this->delivered += 1;
                    $this->viewed += 1;
                    $phone['status'] = 'viewed';
                    $phone['date_viewed'] =time();
                    $changed = true;
                }
            } elseif ($phone['status'] === 'delivered') {
                if ($vb_Note->type == 'seen') {
                    $this->viewed += 1;
                    $phone['status'] = 'viewed';
                    $phone['date_viewed'] =time();
                    $changed = true;
                }
            }
        }
        if ($changed) {
            if ($this->delivered >=$this->size && $this->status == 'new'){
                $this->status='delivered';
            }
            if ($this->viewed >=$this->size && $this->status != 'ready'){
                $this->status='ready';
            }
            $phonesArray[$phonesArray[$vb_Note->msg_id]] = $phone;
            $this->phones = \GuzzleHttp\json_encode($phonesArray);
            $this->save();
        }
    }

    public function Phone($json){
        return json_encode($json);
    }
    
    public function getMessagePhoneList(){
        $this->hasMany(Message_Phone_List::className(),['transaction_id'=>'id']);
    }

}
