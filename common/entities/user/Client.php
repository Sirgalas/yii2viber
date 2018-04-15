<?php
/**
 * @propery Balance[] $costProvider
 */

namespace common\entities\user;

use common\entities\Balance;
use Helper\Extended;
use dektrium\user\helpers\Password;
class Client extends User
{
    /**
     * @param string $type
     * @return \yii\db\ActiveQuery
     */
    public static function getMyClients($type='all'){
        $ids = getChildList();
        if ($ids === -1){
            return [];
        }
        return self::find()->andWhere(['in','id', 'ids'])->asArray()->all();
    }

    public function beforeValidate()
    {
        if ($this->scenario === 'default' && !$this->password && !$this->password_hash){
            $this->password = Password::generate(8);
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
    public function beforeSave($insert)
    {
        if($this->type==$this->isDealer()&&$this->want_dealer==self::WANT)
            $this->want_dealer=self::NOT_WANT;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getCostProvider(){
        return $this->hasOne(Balance::class,['user_id'=>'id']);
    }
}