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
 * @property int $watsapp
 * @property int $telegram
 * @property int $wechat
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
            [['user_id', 'viber', 'watsapp', 'telegram', 'wechat'], 'default', 'value' => null],
            [['user_id', 'viber', 'watsapp', 'telegram', 'wechat'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'viber' => 'Viber',
            'watsapp' => 'Watsapp',
            'telegram' => 'Telegram',
            'wechat' => 'Wechat',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
