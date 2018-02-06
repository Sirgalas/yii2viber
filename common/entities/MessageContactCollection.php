<?php

namespace common\entities;

use Yii;

/**
 * This is the model class for table "message_contact_collection".
 *
 * @property int $id
 * @property int $contact_collection_id
 * @property string $title
 * @property string $type
 * @property int $created_at
 * @property int $viber_message_id
 *
 * @property ContactCollection $contactCollection
 * @property ViberMessage $viberMessage
 */
class MessageContactCollection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_contact_collection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contact_collection_id', 'created_at', 'viber_message_id'], 'default', 'value' => null],
            [['contact_collection_id', 'created_at', 'viber_message_id'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 10],
            [['contact_collection_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContactCollection::className(), 'targetAttribute' => ['contact_collection_id' => 'id']],
            [['viber_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViberMessage::className(), 'targetAttribute' => ['viber_message_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_collection_id' => 'Contact Collection ID',
            'title' => 'Title',
            'type' => 'Type',
            'created_at' => 'Created At',
            'viber_message_id' => 'Viber Message ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactCollection()
    {
        return $this->hasOne(ContactCollection::className(), ['id' => 'contact_collection_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViberMessage()
    {
        return $this->hasOne(ViberMessage::className(), ['id' => 'viber_message_id']);
    }

    /**
     * @inheritdoc
     * @return MessageContactCollectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessageContactCollectionQuery(get_called_class());
    }
}
