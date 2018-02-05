<?php

namespace common\entities;

use Yii;

/**
 * This is the model class for table "contact_collection".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $type
 * @property int $created_at
 *
 * @property User $user
 */
class ContactCollection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_collection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'default', 'value' => null],
            [['user_id', 'created_at'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 10],
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
            'user_id' => 'Клиент',
            'title' => 'Название Коллекции',
            'type' => 'Type',
            'created_at' => 'Дата создания',
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
     * @return ContactCollectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContactCollectionQuery(get_called_class());
    }
}
