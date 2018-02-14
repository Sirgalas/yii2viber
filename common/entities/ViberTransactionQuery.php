<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[ViberTransaction]].
 *
 * @see ViberTransaction
 */
class ViberTransactionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ViberTransaction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ViberTransaction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
