<?php

use yii\db\Migration;

/**
 * Class m180220_132048_alter_contact_collection_add_size
 */
class m180220_132048_alter_contact_collection_add_size extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%contact_collection}}', 'size',$this->integer()->after('created_at'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%contact_collection}}', 'size');

        return false;
    }
}
