<?php
namespace common\entities\mongo;

use Aws\CloudFront\Exception\Exception;
use Yii;
use common\entities\ContactCollection;
use yii\mongodb\ActiveRecord;
use yii\web\User;
use common\entities\ViberMessage;

/**
 * This is the model class for table "phone".
 *
 * @property string $_id
 * @property int $clients_id
 * @property integer $contact_collection_id
 * @property int $phone
 * @property int $username;
 *
 * @property User $user
 * @property ViberMessage $currentMessage
 */

class Phone extends ActiveRecord
{
   /* public $contact_collection_id;
    public $phone;
    public $clients_id;
    public $username;*/

    public static function collectionName()
    {
        return 'phone';
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return ['_id','contact_collection_id','phone','clients_id','username'];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'clients_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getContactCollection(){
        return $this->hasOne(ContactCollection::className(),['id'=>'contact_collection_id']);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'contact_collection_id' => 'ID коллекции контактов',
            'clients_id' => 'ID клиента собственника базы',
            'username' => 'Имя владельца номера',
        ];
    }

    public static function NormalizeNumber($phone)
    {
        return preg_replace('~\D+~', '', $phone);
    }

    public function removeList($collection_id, $ids){
        $list=[];
        foreach ($ids as   $ind) {

                $list[] = $ind;
        }
        try {
            self::deleteAll(['_id'=> $ids]);
        }catch (\Exception $e) {
                return $e->getMessage();
            }
        return 'ok';
    }

    public function importText($collection_id, $txt, $user_id = 0)
    {
        if (! $user_id) {
            $user_id = Yii::$app->user->id;
        }
        $list = str_replace(["\r\n", "\r", "\n"], ',', strip_tags($txt));
        $aList = array_unique(explode(',', $list));
        $bList = [];
        foreach ($aList as $ind => $phone) {
            $v = static::NormalizeNumber($phone);
            if ($v) {
                $bList[] = $v;
            }
        }

        $oldList = self::find()->select(['phone'])->where(['contact_collection_id' => $collection_id])->andWhere([
            'in',
            'phone',
            $aList,
        ])->column();
        if (count($oldList) > 0) {
            $bList = array_diff($bList, $oldList);
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $data = [];
        foreach ($bList as $phone) {
            $data[] = ['clients_id'=>$user_id, 'contact_collection_id'=>$collection_id, 'phone'=>$phone];
        }
        try {
            if(!Yii::$app->mongodb->getCollection('phone')->batchInsert($data))
                throw new Exception('not save');
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }

        return 'ok';
    }

}
