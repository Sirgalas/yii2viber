<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 04.02.2018
 * Time: 14:42
 */

namespace common\entities\user;

use common\entities\ContactCollection;
use common\entities\Phone;
use common\entities\ViberMessage;
use dektrium\user\models\User as BaseUser;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property string $type
 * @property int $dealer_id
 * @property string $balance
 * @property bool $dealer_confirmed
 * @property string $image
 * @property integer want_dealer
 * @property  number cost
 * @property  string tel
 * @property  string time_work
 * @property  string first_name
 * @property  string surname
 * @property  string family
 * @property  string avatar
 * @property ContactCollection[] $contactCollections
 * @property Phone[] $phones
 * @property ViberMessage[] $viberMessages
 */
class User extends BaseUser
{
    const WANT = 1;

    const NOT_WANT = 0;

    const SCENARIO_PROFILE = 'profile';

    const ADMIN = 'admin';

    const CLIENT = 'client';

    const DEALER = 'dealer';

    public static $userTypes = [
        self::ADMIN => 'Админ',
        self::CLIENT => 'Клиент',
        self::DEALER => 'Дилер',
    ];

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
        $scenarios[self::SCENARIO_PROFILE] = [
            'tel',
            'first_name',
            'surname',
            'family',
            'time_work',
            'username',
            'email',
        ];

        return $scenarios;
    }

    public function rules()
    {
        if (! Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
            $ranges = ['admin', 'client', 'dealer'];
        } else {
            $ranges = ['client', 'dealer'];
        }
        $rules = parent::rules();
        // add some rules
        $rules['fieldRequired'] = ['type', 'required'];
        $rules['typeLength'] = ['type', 'in', 'range' => $ranges];

        $rules['balance'] = ['balance', 'integer'];
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
        $rules['cost'] = ['cost', 'number'];
        $rules['tel'] = ['tel', 'string'];
        $rules['first_name'] = ['first_name', 'string', 'max' => 100];
        $rules['surname'] = ['surname', 'string', 'max' => 100];
        $rules['family'] = ['family', 'string', 'max' => 100];
        $rules['avatar'] = ['avatar', 'string'];

        return $rules;
    }

    public function getTheStatus()
    {
        return $this::$userTypes[$this->type];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['type'] = 'Тип';
        $labels['balance'] = 'Баланс';
        $labels['dealer_confirmed'] = 'Статус дилера';
        $labels['dealer_id'] = 'Родительский дилер';
        $labels['image'] = 'Аватар';
        $labels['cost'] = 'Цена за сообщение';
        $labels['tel'] = 'Телефон';
        $labels['time_work'] = 'Время работы';
        $labels['first_name'] = 'Время работы';
        $labels['surname'] = 'Время работы';
        $labels['family'] = 'Время работы';
        $labels['username'] = 'Логин';
        $labels['cost'] = 'Цена';
        $labels['created_at'] = 'Зарегистрирован';

        return $labels;
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

    public function getClient()
    {
        return $this->type === 'client';
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

                SELECT * FROM r WHere id=".$this->id;
        $user = Yii::$app->db->createCommand($sql)->queryOne();
        if ($user) {
            return true;
        }

        return false;
    }

    public function getChildList($type = '')
    {
        if (! $type) {
            $type = '\'client\',\'dealer\'';
        }
        if ($this->isClient()) {
            return Yii::$app->user->id;
        }
        if ($this->isAdmin()) {
            return -1;
        }
        if ($this->isDealer()) {
            $sql = 'WITH RECURSIVE r AS (
                    SELECT id, dealer_id 
                    FROM "user"
                    WHERE id = '.Yii::$app->user->id.'

                    UNION

                    SELECT "user".id, "user".dealer_id 
                    FROM "user"
                    JOIN r
                    ON "user".dealer_id = r.id  and type in ('.$type.')
                )

                SELECT id FROM r WHERE id!='.Yii::$app->user->id;
        }
        $user_ids = Yii::$app->db->createCommand($sql)->queryColumn();
        if ($user_ids && ! is_array($user_ids)) {
            $user_ids = [$user_ids];
        }

        return $user_ids;
    }

    public function whoDealer()
    {
        if ($this->dealer_id) {
            return $this->dealer_id;
        }

        return false;
    }

    public function beforeValidate()
    {
        if (! $this->type && $this->scenario === 'register') {
            $this->type = 'client';
            $this->want_dealer = self::NOT_WANT;
            $this->dealer_id = Yii::$app->params['defaultDealer'];
        }

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactCollections()
    {
        return $this->hasMany(ContactCollection::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhones()
    {
        return $this->hasMany(Phone::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViberMessages()
    {
        return $this->hasMany(ViberMessage::class, ['user_id' => 'id']);
    }

    /**
     * Возвращает список дилеров среди клиентов текущего пользователя
     *
     * @return array
     */
    public function getMyDealers()
    {
        $dealersIds = static::getChildList('\'dealer\'');
        if ($dealersIds === -1) {
            $where = ['type' => 'dealer'];
        } else {
            $where = ['in', 'id', $dealersIds];
        }
        $dealers = static::find()->where($where)->select(['id', 'username'])->asArray()->orderBy('username')->all();

        $dealersDropdown = [Yii::$app->user->id => Yii::$app->user->identity->username];
        foreach ($dealers as $dealer) {
            $dealersDropdown[$dealer['id']] = $dealer['username'];
        }

        return $dealersDropdown;
    }

    public function headerInfo()
    {
        return 'Ваш баланс = '.number_format($this->balance).' SMS ';
    }
}