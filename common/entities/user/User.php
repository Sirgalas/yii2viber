<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 04.02.2018
 * Time: 14:42
 */

namespace common\entities\user;

use dektrium\user\models\User as BaseUser;
use Yii;
/**
 * @property mixed type
 */
class User extends BaseUser
{
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        $scenarios['create'][] = 'dealer_id';
        $scenarios['create'][] = 'image';
        $scenarios['create'][] = 'type';
        $scenarios['update'][] = 'dealer_id';
        $scenarios['update'][] = 'dealer_confirmed';
        $scenarios['update'][] = 'image';
        $scenarios['update'][] = 'balance';
        $scenarios['register'][] = 'dealer_id';
        $scenarios['register'][] = 'image';
        $scenarios['register'][] = 'type';

        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        $rules['fieldRequired'] = ['type', 'required'];
        $rules['typeLength'] = ['type', 'in', 'range' => range('admin', 'client', 'dealer')];
        $rules['balance'] = ['balance', 'money'];
        $rules['image'] = ['image', 'string', 'max' => 255];
        $rules['dealer_confirmed'] = ['dealer_confirmed', 'boolean'];
        $rules['dealer_id'] = ['dealer_id', 'integer'];
        $rules['typeLength'] = [
            'dealer_id',
            'exist',
            'skipOnError' => true,
            'targetClass' => self::className(),
            'targetAttribute' => ['dealer_id' => 'id'],
        ];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['type'] = 'Тип';
        $labels['balance'] = 'Баланс';
        $labels['dealer_confirmed'] = 'Статус дилера';
        $labels['dealer_id'] = 'Родительский дилер';
        $labels['image'] = 'Аватар';
    }

    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    public function isClient()
    {
        return $this->type === 'client';
    }

    public function isDealer()
    {
        return $this->type === 'dealer';
    }

    public function findChilds()
    {
    }

    /**
     * Является ли переданный id - дочерним для текущего пользователя
     *
     * @param $child_id
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\db\Exception
     */
    public function amParent($child_id)
    {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->isClient()) {
            return false;
        }
        $sql = "WITH RECURSIVE r AS (
                    SELECT id, dealer_id, username
                    FROM \"user\"
                    WHERE id = $child_id

                    UNION

                    SELECT \"user\".id, \"user\".dealer_id, \"user\".username
                    FROM \"user\"
                    JOIN r
                    ON \"user\".id = r.dealer_id
                )

                SELECT * FROM r WHere id=" . $this->id;
        $user = Yii::$app->db->createCommand($sql)->queryOne();
        if ($user) {
            return true;
        }
        return false;
    }
}