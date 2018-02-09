<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09.02.18
 * Time: 14:09
 */

namespace frontend\forms;

use yii\base\Model;
class ContactCollectionModalForm extends Model
{
    public $collection_id;
    public $some_collection;
    public function rules()
    {
       return[
           ['collection_id', 'required'],
           [['collection_id','some_collection'], 'integer']
       ];
    }
}