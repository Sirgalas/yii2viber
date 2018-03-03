<?php

namespace common\entities;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property string $param
 * @property string $text
 * @property string $description
 */
class Config extends \yii\db\ActiveRecord
{
    const PATH='web/images';
    const URL='images';
    public $upload_file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param', 'description'], 'string', 'max' => 255],
            [['text'], 'string', 'max' => 5000],
            [['upload_file'], 'file', 'skipOnEmpty' => true,'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'param' => 'Param',
            'text' => 'Text',
            'description' => 'Description',
        ];
    }
    public function upload()
    {
        if ($this->validate()) {
            $this->upload_file->saveAs( $this->uploadPath(). $this->upload_file->baseName . '.' . $this->upload_file->extension);
            $result=$this->upload_file->baseName . '.' . $this->upload_file->extension;
            $this->upload_file=null;
            return $result;
        } else {
            return false;
        }
    }

    public function uploadPath(){
        return Yii::getAlias('@frontend').'/'.self::PATH . '/';
    }

    public function getImagePath(){
        return $this->uploadPath().$this->text;
    }

    public function getUploadUrl(){
        return Yii::$app->params['frontendHostInfo'].'/'.self::URL;
    }
    
    public function getImageUrl(){
        return $this->uploadUrl . '/'.$this->text;
    }
}
