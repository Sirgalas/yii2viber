<?php

use yii\db\Migration;

/**
 * Class m180220_130821_add_column_user_feild
 */
class m180220_130821_add_column_user_feild extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','first_name',$this->string(100));
        $this->addColumn('user','surname',$this->string(100));
        $this->addColumn('user','family',$this->string(100));
        $this->addColumn('user','avatar',$this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user','first_name');
        $this->dropColumn('user','surname');
        $this->dropColumn('user','family');
        $this->dropColumn('user','avatar');
    }

}
