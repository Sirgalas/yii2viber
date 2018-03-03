<?php

use yii\db\Migration;

/**
 * Class m180228_134716_alter_viber_message_add_payment_status
 */
class m180228_134716_alter_viber_message_add_payment_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_message}}', 'wait_payment_comment',$this->string(255)->after('type'));
        $this->addColumn('{{%viber_message}}', 'admin_comment',$this->string(255)->after('type'));
        $this->addColumn('{{%viber_message}}', 'provider',$this->string(16)->after('type'));
        $this->addColumn('{{%user}}', 'admin_comment',$this->string(1024)->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_message}}', 'wait_payment_comment');
        $this->dropColumn('{{%viber_message}}', 'admin_comment');
        $this->dropColumn('{{%viber_message}}', 'provider');
        $this->dropColumn('{{%user}}', 'admin_comment');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180228_134716_alter_viber_message_add_payment_status cannot be reverted.\n";

        return false;
    }
    */
}
