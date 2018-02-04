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
}
