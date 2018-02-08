<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 07.02.2018
 * Time: 11:54
 */
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $phoneFile;
    public $contact_collection_id;
    public function rules()
    {
        return [
            [['phoneFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt, csv'],
            ['contact_collection_id', 'integer'],
            ['contact_collection_id', 'required'],
            [
                ['contact_collection_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ContactCollection::className(),
                'targetAttribute' => ['contact_collection_id' => 'id'],
            ],
        ];
    }

    public function upload()
    {

    }
}