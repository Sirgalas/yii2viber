<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[MessageContactCollection]].
 *
 * @see MessageContactCollection
 */
class MessageContactCollectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MessageContactCollection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MessageContactCollection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
