<?php

use yii\db\Migration;

/**
 * Class m180207_001646_create_phone_tables
 */
class m180207_001646_create_phone_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%phone}}',[
            'id'=>$this->primaryKey(),
            'user_id'=>$this->integer()->notNull(),
            'username'=>$this->string(),
            'phone'=>$this->integer(),
            'contact_collection_id'=>$this->integer()
        ]);

        $this->addForeignKey('{{%fk_phone_user}}', '{{%phone}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%fk_contact_collection_user}}', '{{%phone}}', 'contact_collection_id', '{{%contact_collection}}', 'id', 'CASCADE', 'CASCADE');
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone}}');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180207_001646_create_phone_tables cannot be reverted.\n";

        return false;
    }
    */
}
