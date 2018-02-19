<?php

namespace common\entities;

use Yii;
use common\entities\user\User;

/**
 * This is the model class for table "phone".
 *
 * @property int $id
 * @property int $user_id
 * @property string $username
 * @property int $phone
 * @property int $contact_collection_id
 *
 * @property ContactCollection $contactCollection
 * @property user\User $user
 */
class Phone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'phone', 'contact_collection_id'], 'default', 'value' => null],
            [['user_id', 'phone', 'contact_collection_id'], 'integer'],
            [['username'], 'string', 'max' => 255],
            [
                ['contact_collection_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ContactCollection::class,
                'targetAttribute' => ['contact_collection_id' => 'id'],
            ],
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
            'username' => 'Username',
            'phone' => 'Phone',
            'contact_collection_id' => 'Contact Collection ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactCollection()
    {
        return $this->hasOne(ContactCollection::class, ['id' => 'contact_collection_id']);
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
     * @return PhoneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PhoneQuery(get_called_class());
    }

    public static function NormalizeNumber($phone)
    {
        return preg_replace('~\D+~', '', $phone);
    }

    public function importText($collection_id, $txt, $user_id = 0)
    {
        if (! $user_id) {
            $user_id = Yii::$app->user->id;
        }
        $list = str_replace(["\r\n", "\r", "\n"], ',', strip_tags($txt));
        $aList = array_unique(explode(',', $list));
        $bList = [];
        foreach ($aList as $ind => $phone) {
            $v = static::NormalizeNumber($phone);
            if ($v) {
                $bList[] = $v;
            }
        }
        $oldList = self::find()->select(['phone'])->where(['contact_collection_id' => $collection_id])->andWhere([
                                                                                                                     'in',
                                                                                                                     'phone',
                                                                                                                     $aList,
                                                                                                                 ])->column();
        if (count($oldList) > 0) {
            $bList = array_diff($bList, $oldList);
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $data = [];
        foreach ($bList as $phone) {
            $data[] = [$user_id, $collection_id, $phone];
        }
        try {
            $db->createCommand()->batchInsert('phone', ['user_id', 'contact_collection_id', 'phone'], $data)->execute();
            $transaction->commit();
        } catch (\Exception $e) {

            $transaction->rollBack();

            return $e->getMessage();
        }

        return 'ok';
    }

    public function removeList($collection_id, $ids)
    {
        $list=[];
        foreach ($ids as   $ind) {
            $v = static::NormalizeNumber($ind);
            if ($v) {
                $list[] = $v;
            }
        }
        $ids=implode(',', $list);
        try {
            self::deleteAll([
                                'and',
                                'contact_collection_id = :contact_collection_id',
                                'id in ( ' . $ids . ') '
                            ], [
                                ':contact_collection_id' => $collection_id

                            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return 'ok';
    }
}
