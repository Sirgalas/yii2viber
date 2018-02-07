<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[Phone]].
 *
 * @see Phone
 */
class PhoneQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Phone[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Phone|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
