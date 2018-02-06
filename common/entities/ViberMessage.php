<?php

namespace common\entities;


use common\entities\user\User;
/**
 * This is the model class for table "viber_message".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $text
 * @property string $image
 * @property string $title_button
 * @property string $url_button
 * @property string $type
 * @property string $alpha_name
 * @property int $date_start
 * @property int $date_finish
 * @property string $time_start
 * @property string $time_finish
 * @property string $status
 * @property int $limit_messages Сколько сообщений отправлять?
 * @property string $cost Стоимость
 * @property string $balance Сколько средств уже потрачено
 *
 * @property user/User $user
 */
class ViberMessage extends \yii\db\ActiveRecord
{
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
            [['user_id', 'date_start', 'date_finish', 'limit_messages'], 'integer'],
            [['title', 'text', 'image', 'title_button', 'url_button'], 'required'],
            [['cost', 'balance'], 'number'],
            [['title'], 'string', 'max' => 50],
            [['text'], 'string', 'max' => 120],
            [['image', 'url_button'], 'string', 'max' => 255],
            [['title_button', 'alpha_name'], 'string', 'max' => 32],
            [['type'], 'string', 'max' => 10],
            ['type', 'in', 'range' => array_keys(static::listTypes())],
            [['time_start', 'time_finish'], 'string', 'max' => 5],
            [['status'], 'string', 'max' => 16],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
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
            'title' => 'Title',
            'text' => 'Text',
            'image' => 'Image',
            'title_button' => 'Title Button',
            'url_button' => 'Url Button',
            'type' => 'Type',
            'alpha_name' => 'Alpha Name',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'time_start' => 'Time Start',
            'time_finish' => 'Time Finish',
            'status' => 'Status',
            'limit_messages' => 'Limit Messages',
            'cost' => 'Cost',
            'balance' => 'Balance',
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
     * @inheritdoc
     * @return ViberMessageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ViberMessageQuery(get_called_class());
    }

    public static function listTypes()
    {
        return [
            'only_text' => 'Только текст (Официально)',
            'only_image' => 'Только изображение (Официально)',
            'text_and_button' => 'Текст + кнопка (Официально)',
            'text_and_button_and_image' => 'Текст + кнопка + изображение (Официально)',
        ];
    }

    public static function getTypeText($ind)
    {
        $list = static::listTypes();

        return $list[$ind];
    }

    public static function getTypeComment($ind)
    {
        $list = [
            'only_text' => 'абонент получает текст',
            'only_image' => 'абонент получает картинку',
            'text_and_button' => 'абонент получает текстовое сообщение, под которым расположена кнопка. При нажатии на кнопку – происходит переход по заданной ссылке',
            'text_and_button_and_image' => 'абонент получает текстовое сообщение, под которым расположены картинка и кнопка',
        ];

        return $list[$ind];
    }
}
