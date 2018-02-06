<?php
namespace frontend\services\phone;


use frontend\forms\PhoneUpdateForm;

use common\entities\mongo\Phone;
use PHPUnit\Framework\MockObject\RuntimeException;

class PhoneFormUpdateService
{
    public function update(PhoneUpdateForm $form, Phone $phones){
        $array=['+','(',')','-'];
        if(($phone=Phone::find()->where(['phone'=>$form->phone,'contact_collection_id'=>$form->contact_collection_id])->one())!=null)
            throw new \RuntimeException('Phone already exist');
        $phone->contact_collection_id=$form->contact_collection_id;
        $phone->phone=(int)str_replace($array,'',$form->phone);
        $phone->username=$form->username;
        $phone->clients_id=$form->clients_id;

        if(!$phone->save())
            throw new RuntimeException(json_encode($phone->errors));
        return $phone;
    }
}