<?php

use yii\db\Migration;

/**
 * Class m180205_061548_create_table_contact_collection
 */
class m180205_061548_create_table_contact_collection extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%contact_collection}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'title' => $this->string(50)->notNull(),
            'type' => $this->string(10)->notNull()->defaultValue('viber'),

            'created_at' => $this->integer(),

        ]);

        $this->addForeignKey('{{%fk_contact_collection_client}}', '{{%contact_collection}}', 'user_id', '{{%user}}',
                             'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%contact_collection}}');
        return false;
    }
}
