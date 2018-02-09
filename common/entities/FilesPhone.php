<?php

namespace common\entities;

use common\entities\user\User;
use Yii;

/**
 * This is the model class for table "files_phone".
 *
 * @property int $id
 * @property int $user_id
 * @property string $file
 * @property int $month
 * @property int $years
 */
class FilesPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files_phone';
    }

    public static $PATH = 'files/phone/exel';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'month', 'years'], 'default', 'value' => null],
            [['user_id', 'month', 'years'], 'integer'],
            [['file'], 'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->user_id=Yii::$app->user->identity->id;
            $this->years=date('Y',time());
            $this->month=date('m',time());
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file' => 'File',
            'month' => 'Month',
            'years' => 'Years',
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
}