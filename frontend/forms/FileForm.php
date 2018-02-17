<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.02.18
 * Time: 10:51
 */

namespace frontend\forms;

use yii\base\Model;
use Yii;
class FileForm extends Model
{
    public $file;
    public $fieldPhone;
    public $fieldUsername;
    public $delimiter;
    public $collection_id;
    public $first_row;

    const SCENARIO_EXEL='exel';
    const SCENARIO_OUTHER='outher';
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EXEL] = ['file', 'fieldPhone','fieldUsername','collection_id','first_row'];
        $scenarios[self::SCENARIO_OUTHER] = ['file', 'field','colection_id','delimiter'];
        return $scenarios;
    }

    public function rules()
    {
       return[
           [['file','fieldPhone','fieldUsername','collection_id',],'required'],
           [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv, txt, xls,xlsx'],
           [['delimiter','first_row'],'safe'],
           [['collection_id','field'],'integer'],
       ];
    }

    public function attributeLabels()
    {
        return[
            'fieldPhone'=>Yii::t('front','столбец с телефонами'),
            'fieldUsername'=>Yii::t('front','столбец с именами'),
            'first_row'=>Yii::t('front','разделитель столбцев'),
            'delimiter'=>Yii::t('front','разделитель строк')
        ];

    }

}