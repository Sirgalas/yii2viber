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
 * @property int $whatsapp
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
            [['user_id', 'viber', 'telegram', 'whatsapp', 'wechat'], 'default', 'value' => null],
            [['user_id', 'viber', 'telegram', 'whatsapp', 'wechat'], 'integer', 'min' => 0],

            [['viber_price', 'whatsapp_price', 'telegram_price', 'wechat_price'], 'string', 'max' => 20],
            [
                ['user_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
                'targetAttribute' => ['user_id' => 'id']],

            'check_balance' => [
                ['viber', 'telegram', 'wechat', 'whatsapp'],
                function ($attribute, $params) {
                    if (! is_a(Yii::$app, 'yii\console\Application')) {
                        if ($this->user_id != Yii::$app->user->id) {
                            if ($this->getOldAttribute($attribute) < $this->getAttribute($attribute)) {
                                $balance = Yii::$app->user->identity->balance;
                                $result  = -1;
                                if ($balance) {
                                    $result = $balance[$attribute] + $this->getOldAttribute($attribute) - $this->getAttribute($attribute);
                                }

                                if ($result < 0) {
                                    $this->addError($attribute, 'Недостаточно средств');
                                }
                            }
                        }

                        if ($this->user_id == Yii::$app->user->id && $this->scenario != 'own') {
                            if ($this->getOldAttribute($attribute) != $this->getAttribute($attribute)) {
                                $this->addError($attribute, 'Нельзя редактиовать собственный баланс');
                            }
                        }
                    }
                },
                'message' => 'недостаточно средств']];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'user_id'        => 'User ID',
            'viber'          => 'Баланс Viber',
            'telegram'       => 'Баланс Telegram',
            'wechat'         => 'Баланс Wechat',
            'viber_price'    => 'Стоимость Viber',
            'whatsapp'       => 'Баланс Whatsapp',
            'whatsapp_price' => 'Стоимость Whatsapp',
            'telegram_price' => 'Стоимость Telegram',
            'wechat_price'   => 'Стоимость Wechat',];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function scenarios()
    {
        $scenarios        = parent::scenarios();
        $scenarios['own'] = $scenarios['default'];
        unset($scenarios['own']['check_balance']);

        return $scenarios;
    }

    protected function channelRest($channel, $parent)
    {
        $result = (int)$parent->getAttribute($channel) + (int)$this->getOldAttribute($channel) - (int)$this->getAttribute($channel);

        return (integer)$result;
    }

    public function beforeSave($insert)
    {
        if (! is_a(Yii::$app, 'yii\console\Application')) {
            if ($this->user_id != Yii::$app->user->id) {
                $balance = Yii::$app->user->identity->balance;

                if ($balance) {

                    $balance->viber    = $this->channelRest('viber', $balance);
                    $balance->sms      = $this->channelRest('sms', $balance);
                    $balance->whatsapp = $this->channelRest('whatsapp', $balance);
                    $balance->telegram = $this->channelRest('telegram', $balance);
                    if ($balance->viber >= 0 && $balance->whatsapp >= 0 && $balance->telegram >= 0) {
                        $balance->setScenario('own');
                        if ($balance->validate() && $balance->save()) {
                            return parent::beforeSave($insert);
                        }
                    }
                }

                return false;
            }
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
