<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[ViberMessage]].
 *
 * @see ViberMessage
 */
class ViberMessageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ViberMessage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ViberMessage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function justNow(){
        return $this->andWhere(['<=','date_start',time() ])
            ->andWhere(['>=','date_finish',time() ])
            ->andWhere(['<=','time_start',date('H:i') ])
            ->andWhere(['>=','time_finish',date('H:i') ])
            ;
    }
    public function isNew()
    {
        return $this->andWhere(['status'=>ViberMessage::STATUS_NEW ])->justNow();
    }
    public function isProcess()
    {
        return $this->andWhere(['status'=>ViberMessage::STATUS_PROCESS ])->justNow();
    }

}
