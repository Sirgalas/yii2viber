<?php
namespace frontend\services\phone;


use frontend\forms\PhoneUpdateForm;

use common\entities\mongo\Phone;
use PHPUnit\Framework\MockObject\RuntimeException;

class PhoneFormUpdateService
{
    public function update(PhoneUpdateForm $form, Phone $phones){
        if(!Phone::find()->where(['phone'=>$form->phone,'contact_collection_id'=>$form->contact_collection_id])->one())
            throw new \RuntimeException('Phone already exist');
        $phone=$phones->update(
            $form->contact_collection_id,
            $form->phone,
            $form->username,
            $form->clients_id
        );
        if(!$phone->save())
            throw new RuntimeException(json_encode($phone->errors));
        return $phone;
    }
}