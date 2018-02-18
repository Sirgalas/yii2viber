<?php

use yii\db\Migration;

/**
 * Class m180217_140246_add_column_to_viber_transaction
 */
class m180217_140246_add_column_to_viber_transaction extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_transaction}}', 'size',$this->integer()->after('created_at'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_transaction}}', 'size');

        return false;
    }


}
