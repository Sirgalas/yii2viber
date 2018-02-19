<?php

use yii\db\Migration;

/**
 * Class m180219_134941_add_dlr_timeout_to_viber_message
 */
class m180219_134941_add_dlr_timeout_to_viber_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_message}}', 'dlr_timeout',$this->integer()->after('created_at'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_message}}', 'dlr_timeout');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180219_134941_add_dlr_timeout_to_viber_message cannot be reverted.\n";

        return false;
    }
    */
}
