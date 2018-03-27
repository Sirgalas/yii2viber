<?php

namespace backend\models;

use Yii;
use common\entities\user\User;
use common\entities\ViberMessage;
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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'viber_transaction';
    }

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
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['viber_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViberMessage::class, 'targetAttribute' => ['viber_message_id' => 'id']],
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
            'viber_message_id' => 'Viber Message ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'delivered' => 'Delivered',
            'viewed' => 'Viewed',
            'phones' => 'Phones',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViberMessage()
    {
        return $this->hasOne(ViberMessage::class, ['id' => 'viber_message_id']);
    }
}
