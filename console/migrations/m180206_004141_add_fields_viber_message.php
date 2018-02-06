<?php

use yii\db\Migration;

/**
 * Class m180206_004141_add_fields_viber_message
 */
class m180206_004141_add_fields_viber_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_message}}', 'status',$this->string(16)->after('type'));
        $this->addColumn('{{%viber_message}}', 'limit_messages',$this->integer()->after('status')->comment('Сколько сообщений отправлять?'));
        $this->addColumn('{{%viber_message}}', 'cost',$this->money()->after('status')->comment('Стоимость'));
        $this->addColumn('{{%viber_message}}', 'balance',$this->money()->after('cost')->comment('Сколько средств уже потрачено'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_message}}', 'balance');
        $this->dropColumn('{{%viber_message}}', 'cost');
        $this->dropColumn('{{%viber_message}}', 'limit_messages');
        $this->dropColumn('{{%viber_message}}', 'status');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180206_004141_add_fields_viber_message cannot be reverted.\n";

        return false;
    }
    */
}
