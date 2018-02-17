<?php

namespace common\entities;

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
 * @property int $delivered
 * @property int $viewed
 * @property string $phones
 *
 * @property User $user
 * @property ViberMessage $viberMessage
 */
class ViberTransaction extends \yii\db\ActiveRecord
{

    public $titleSearch;
    public $contactCollection;
    public $status;
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
            [['user_id', 'viber_message_id', 'created_at', 'delivered', 'viewed'], 'integer'],
            [['phones'], 'string'],
            [['status'], 'string', 'max' => 60],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['viber_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViberMessage::className(), 'targetAttribute' => ['viber_message_id' => 'id']],
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

    public function getPhonesArray(){
        return (array)\GuzzleHttp\json_decode($this->phones);
    }

    public function Phone($json){
        return json_encode($json);
    }

}
