<?php

use yii\db\Migration;

/**
 * Class m180304_193024_add_column_token
 */
class m180304_193024_add_column_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','token',$this->string(12));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user','token');
    }

}
