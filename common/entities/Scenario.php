<?php

namespace common\entities;

use Yii;

/**
 * This is the model class for table "scenario".
 *
 * @property int $id
 * @property string $provider
 * @property string $name
 * @property string $from1
 * @property string $channel1
 * @property string $from2
 * @property string $channel2
 * @property string $from3
 * @property string $channel3
 * @property bool $default
 * @property string $provider_scenario_id
 * @property string $created_at
 */
class Scenario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scenario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider'], 'required'],
            [['default'], 'boolean'],
            [['created_at'], 'safe'],
            [['provider', 'name', 'from1', 'channel1', 'from2', 'channel2', 'from3', 'channel3', 'provider_scenario_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'provider' => Yii::t('app', 'Provider'),
            'name' => Yii::t('app', 'Name'),
            'from1' => Yii::t('app', 'From1'),
            'channel1' => Yii::t('app', 'Channel1'),
            'from2' => Yii::t('app', 'From2'),
            'channel2' => Yii::t('app', 'Channel2'),
            'from3' => Yii::t('app', 'From3'),
            'channel3' => Yii::t('app', 'Channel3'),
            'default' => Yii::t('app', 'Default'),
            'provider_scenario_id' => Yii::t('app', 'Provider Scenario ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ScenarioQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ScenarioQuery(get_called_class());
    }
}
