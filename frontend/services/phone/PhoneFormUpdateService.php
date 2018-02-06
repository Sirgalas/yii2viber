<?php
namespace frontend\services\phone;


use frontend\forms\PhoneUpdateForm;

use common\entities\mongo\Phone;

class PhoneFormUpdateService
{
    public function update(PhoneUpdateForm $form, Phone $phone){
        $array=['+','(',')','-'];
        $phone->contact_collection_id=$form->contact_collection_id;
        $phone->phone=(int)str_replace($array,'',$form->phone);
        $phone->username=$form->username;
        $phone->clients_id=$form->clients_id;
        if(!$phone->save())
            throw new \RuntimeException(json_encode($phone->errors));
        return $phone;
    }
}