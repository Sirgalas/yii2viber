<?php

use yii\db\Migration;

/**
 * Class m180205_081537_create_message_contact_collection
 */
class m180205_081537_create_message_contact_collection extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%message_contact_collection}}', [
            'id' => $this->primaryKey(),
            'contact_collection_id' => $this->integer()->null(),
            'title' => $this->string(50)->notNull(),
            'type' => $this->string(10)->notNull()->defaultValue('viber'),

            'created_at' => $this->integer(),

        ]);

        $this->addForeignKey('{{%fk_message_contact_collection}}', '{{%message_contact_collection}}', 'contact_collection_id', '{{%contact_collection}}',
                             'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%message_contact_collection}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180205_081537_create_message_contact_collection cannot be reverted.\n";

        return false;
    }
    */
}
