<?php

use yii\db\Migration;

/**
 * Class m180218_024054_alter_viber_message_add_field_date_send
 */
class m180218_024054_alter_viber_message_add_field_date_send extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_transaction}}', 'date_send',$this->integer()->after('created_at'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_transaction}}', 'date_send');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180218_024054_alter_viber_message_add_field_date_send cannot be reverted.\n";

        return false;
    }
    */
}
