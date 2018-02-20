<?php
namespace common\entities\mongo;

use PhpOffice\PhpSpreadsheet\IOFactory;
use frontend\forms\FileForm;
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

    const EXEL2007='xlsx';
    const EXEL='xls';

    public static $Reader=[
        self::EXEL2007=> 'Xlsx',
        self::EXEL=>'Xls'
    ];

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
        return $this->hasOne(User::class,['id'=>'clients_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getContactCollection(){
        return $this->hasOne(ContactCollection::class,['id'=>'contact_collection_id']);
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

    public function removeList($ids){
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

    public function pointer($resource,$post,$arrayPost,FileForm $form){
        $entities= new ContactCollection();
        $result=false;
        if(in_array($resource->extension,$entities->fileExel())){
            $form->scenario=FileForm::SCENARIO_EXEL;
            if($form->load($post))
                $result=$this->importExel($resource->tempName,$arrayPost,$resource->getExtension());
        }
        if($resource->extension=='csv'){
            $form->scenario=FileForm::SCENARIO_OUTHER;
            if($form->load($post))
                $result=$this->importCsv($resource->tempName,$arrayPost);
        }
        if($resource->extension=='txt'){
            $form->scenario=FileForm::SCENARIO_OUTHER;
            if($form->load($post))
                $result=$this->importTxt($resource->tempName,$arrayPost);
        }
        return $result;
    }

    private function importExel($file,$post,$extension){
        $reader=IOFactory::createReader(self::$Reader[$extension]);
        $reader->setReadDataOnly(true);
        $objPHPExcel = $reader->load($file);
        if(!$post['fieldPhone'])
            throw new \Exception('не указано поле телефонов');
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            for ($row = 1; $row <= $worksheet->getHighestRow(); ++ $row)
            {
                $phone[$worksheet->getCell($post['fieldPhone'].$row)->getValue()]=$worksheet->getCell($post['fieldUsername'].$row)->getValue();
            }
        }
        $phoneNumber=Phone::find()->select(['phone'])->where(['contact_collection_id'=>$post['collection_id']])->column();
        foreach ($phoneNumber as $numberPhone){
            unset($phone[$numberPhone]);
        }
        if(!$phone)
            throw new \Exception('нет уникальных телефонов');
        foreach ($phone as $key=>$val) {
            $datas[] = ['clients_id'=>Yii::$app->user->identity->id, 'contact_collection_id'=>$post['collection_id'], 'phone'=>static::NormalizeNumber($key),'username'=>($val)?htmlspecialchars(trim($val)):''];
        }
        unset($phone);
        if($this->saveDate($datas)== 'ok')
            return $post['collection_id'];
        return false;
    }



    private function importCsv($file,$post){
        if (($handle = fopen($file, "r")) !== FALSE) {
            foreach(fgetcsv($handle,0, $post['delimiter']) as $arrExplode){
                $arrForData[]=explode($post['first_row'],$arrExplode);
            }
            foreach ($arrForData as $data){
                if(array_key_exists($post['fieldPhone'],$data))
                    $searchList[]=static::NormalizeNumber($data[$post['fieldPhone']]);
            }
            $arrForData=$this->arrayDiff($searchList,$arrForData,$post['collection_id']);
            if(!$arrForData)
                throw new \Exception('нет уникальных телефонов');
            foreach ( $arrForData as $data) {
                if(array_key_exists($post['fieldPhone'],$data))
                $datas[] = ['clients_id'=>Yii::$app->user->identity->id, 'contact_collection_id'=>$post['collection_id'], 'phone'=>static::NormalizeNumber($data[$post['fieldPhone']]),'username'=>array_key_exists($post['fieldUsername'],$data)?htmlspecialchars(trim($data[$post['fieldUsername']])):''];
            }
            if($this->saveDate($datas)== 'ok')
                return $post['collection_id'];
            return false;
        }else{
            throw new \Exception('фаил не читаемый');
        }
    }

    private function importTxt($file,$post){
            $txt=file_get_contents($file);
            $list = str_replace(["\r\n", "\r", "\n"], ',', strip_tags($txt));
            $aList = array_unique(explode(',', $list));
            $searchList=array();
            foreach ($aList as $list){
                $exp[] = explode($post['first_row'],$list);
            }
            foreach ($exp as $data){
                if(array_key_exists($post['fieldPhone'],$data))
                $searchList[]=static::NormalizeNumber($data[$post['fieldPhone']]);
            }
            $exp=$this->arrayDiff($searchList,$exp,$post['collection_id']);
            if(!$exp)
                throw new \Exception('нет уникальных телефонов');

             foreach ($exp as $data){
                   if(array_key_exists($post['fieldPhone'],$data)){
                       $datas[]=['clients_id'=>Yii::$app->user->identity->id, 'contact_collection_id'=>$post['collection_id'], 'phone'=>static::NormalizeNumber($data[$post['fieldPhone']]),'username'=>array_key_exists($post['fieldUsername'],$data)?htmlspecialchars(trim($data[$post['fieldUsername']])):''];
                   }
            }
        if($this->saveDate($datas)== 'ok')
            return $post['collection_id'];
        return false;
    }

    public function importText($collection_id, $txt, $user_id = 0)
    {
        if (! $user_id) {
            $user_id = Yii::$app->user->id;
        }
        $list = str_replace(["\r\n", "\r", "\n"], ',', strip_tags($txt));
        $aList = array_unique(explode(',', $list));
        $bList = [];
        $searcList=[];
        foreach ($aList as  $phone) {
           if($phone!==""){
               if(strpos($phone,'%'))
               {
                   $arrExplod=explode('%',$phone);
                   $number=static::NormalizeNumber($arrExplod[0]);
                   $name=$arrExplod[1];
               }else{
                   $number=static::NormalizeNumber($phone);
                   $name=null;
               }
               if ($number) {
                   $bList[$number] =$name;
                   $searcList[]=(integer)$number;
               }
           }
        }
        $oldList = self::find()->select(['phone'])->where(['phone'=>$searcList])->andWhere(['contact_collection_id'=>$collection_id])->column();
        if (count($oldList) > 0) {
            foreach($oldList as $first){
                unset($bList[$first]);
            }
        }
        $data = [];
        foreach ($bList as $phone=>$username) {
            $data[] = ['clients_id'=>$user_id, 'contact_collection_id' => $collection_id, 'phone'=>$phone,'username'=>$username];
        }
        return $this->saveDate($data);
    }

    public function importCollection($post){
        $collection=ContactCollection::find()->select('id')->where(['id'=>$post['collection_id'],'user_id'=>Yii::$app->user->identity->id])->column();
        if(!$collection)
            throw new \Exception('Коллекция у пользователя не обнаружена');
        $phones=Phone::find()->where(['contact_collection_id'=>(string)$collection[0]])->all();
        foreach ($phones as $phone){
            $data[] = ['clients_id'=>Yii::$app->user->identity->id, 'contact_collection_id'=>$post['some_collection'], 'phone'=>$phone->phone,'username'=>$phone->username];
            $phoneSearch[]=$phone->phone;
        }
        $oldList = self::find()->select(['phone'])->where(['phone'=>$phoneSearch,'contact_collection_id'=>$post['some_collection']])->column();
        if($oldList){
            foreach ($data as $array){
                if(!array_intersect($oldList,$array)){
                    $result[]=$array;
                }
            }
        }else{
            $result=$data;
        }
        if(!isset($result))
            throw new \Exception('новые телевоны отсутствуют');
        if($this->saveDate($result)== 'ok')
            return $post['some_collection'];
        return false;
    }

    private function arrayDiff($search,$arrays,$contact_collection_id){
        $oldList = self::find()->select(['phone'])->where(['phone'=>$search,'contact_collection_id'=>$contact_collection_id])->column();
        if($oldList){
            $result=[];
            foreach ($arrays as $array){
                if(!array_intersect($oldList,$array)){
                    $result[]=$array;
                }
            }
            return $result;
        }
        return $arrays;
    }

    private function saveDate($data){
        try {
            if(empty($data))
                throw new \Exception(Yii::t('front','Not new user'));
            if(!Yii::$app->mongodb->getCollection('phone')->batchInsert($data))
                throw new \Exception('not save');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return 'ok';
    }

}
