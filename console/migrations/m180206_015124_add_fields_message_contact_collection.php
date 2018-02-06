<?php

use yii\db\Migration;

/**
 * Class m180206_015124_add_fields_message_contact_collection
 */
class m180206_015124_add_fields_message_contact_collection extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%message_contact_collection}}', 'viber_message_id',$this->integer()->after('id'));
        $this->addForeignKey('{{%fk_viber_message_contact_collection}}', '{{%message_contact_collection}}', 'viber_message_id', '{{%viber_message}}', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%message_contact_collection}}', 'viber_message_id');

        return false;
    }


}
