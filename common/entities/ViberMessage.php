<?php

namespace common\entities;

use Yii;

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
 *
 * @property User $user
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
            [['user_id', 'date_start', 'date_finish'], 'default', 'value' => null],
            [['user_id', 'date_start', 'date_finish'], 'integer'],
            [['title', 'text', 'image', 'title_button', 'url_button'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['text'], 'string', 'max' => 120],
            [['image', 'url_button'], 'string', 'max' => 255],
            [['title_button', 'alpha_name'], 'string', 'max' => 32],
            [['type'], 'string', 'max' => 10],
            [['time_start', 'time_finish'], 'string', 'max' => 5],
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
}
