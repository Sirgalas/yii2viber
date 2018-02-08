<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 04.02.2018
 * Time: 14:42
 */

namespace backend\entities\user;

use common\entities\user\User as BaseUser;

class User extends BaseUser
{
    public static function findIdentity($id)
    {
        return static::find()->where(['id'=>$id])->andWhere([ 'type'=>'admin'])->one();
    }

    public function beforeSave($insert){
        if ($insert && !$this->type){
            $this->setAttribute('type','admin');
        }
        return parent::beforeSave($insert);
    }
}