<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[BalanceLog]].
 *
 * @see BalanceLog
 */
class BalanceLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return BalanceLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return BalanceLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


}
