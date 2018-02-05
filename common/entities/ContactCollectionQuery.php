<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[ContactCollection]].
 *
 * @see ContactCollection
 */
class ContactCollectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactCollection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactCollection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
