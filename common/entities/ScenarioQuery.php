<?php

namespace common\entities;

/**
 * This is the ActiveQuery class for [[Scenario]].
 *
 * @see Scenario
 */
class ScenarioQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Scenario[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Scenario|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
