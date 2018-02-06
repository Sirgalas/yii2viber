<?php


namespace frontend\services\phone;


use common\entities\mongo\Phone;
use frontend\forms\PhoneCreateForm;
class PhoneFormCreateService
{
    public function create(PhoneCreateForm $phoneCreateForm){
        $array=['+','(',')','-'];
        if(Phone::find()->where(['phone'=>$phoneCreateForm->phone,'contact_collection_id'=>$phoneCreateForm->contact_collection_id])->one())
            throw new \RuntimeException('Phone already exist');
        $phone=Phone::createPhone(
            $phoneCreateForm->contact_collection_id,
            (int)str_replace($array,'',$phoneCreateForm->phone),
            $phoneCreateForm->clients_id,
            $phoneCreateForm->username
        );
        if(!$phone->save())
            throw new \RuntimeException(json_encode($phone->errors));
        return $phone;
    }
}