<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.02.18
 * Time: 15:22
 */

namespace frontend\services\phone;


use common\entities\mongo\Phone;
use frontend\forms\PhoneCreateForm;


class PhoneFormCreateService
{
    public function create(PhoneCreateForm $phoneCreateForm){
        if(Phone::find()->where(['phone'=>$phoneCreateForm->phone,'contact_collection_id'=>$phoneCreateForm->contact_collection_id])->one())
            throw new \RuntimeException('Phone already exist');
        $phone=Phone::createPhone(
            $phoneCreateForm->contact_collection_id,
            $phoneCreateForm->phone,
            $phoneCreateForm->username,
            $phoneCreateForm->clients_id
        );
        if(!$phone->save())
            throw new \RuntimeException(json_encode($phone->errors));
        return $phone;

    }
}