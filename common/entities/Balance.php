<?php

namespace common\entities;

use Yii;
use common\entities\user\User;
/**
 * This is the model class for table "balance".
 *
 * @property int $id
 * @property int $user_id
 * @property int $viber
 * @property int $telegram
 * @property int $wechat
 * @property string $viber_price
 * @property string $whatsapp
 * @property string $whatsapp_price
 * @property string $telegram_price
 * @property string $wechat_price
 *
 * @property User $user
 */
class Balance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'viber', 'telegram', 'wechat'], 'default', 'value' => null],
            [['user_id', 'viber', 'telegram', 'wechat'], 'integer'],
            [['viber_price', 'whatsapp', 'whatsapp_price', 'telegram_price', 'wechat_price'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'viber' => 'Баланс Viber',
            'telegram' => 'Баланс Telegram',
            'wechat' => 'Баланс Wechat',
            'viber_price' => 'Стоимость Viber',
            'whatsapp' => 'Баланс Whatsapp',
            'whatsapp_price' => 'Стоимость Whatsapp',
            'telegram_price' => 'Стоимость Telegram',
            'wechat_price' => 'Стоимость Wechat',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class , ['id' => 'user_id']);
    }
}
