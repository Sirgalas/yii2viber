<?php

use yii\db\Migration;

/**
 * Class m180204_032542_messages
 */
class m180204_032542_messages extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%viber_message}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'title' => $this->string(50)->notNull(),
            'text' => $this->string(120)->notNull(),
            'image' => $this->string(255)->notNull(),
            'title_button' => $this->string(32)->notNull(),
            'url_button' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull()->defaultValue('text'),
            'alpha_name' => $this->string(32),
            'date_start' => $this->integer(),
            'date_finish' => $this->integer(),
            'time_start' => $this->string(5),
            'time_finish' => $this->string(5),
        ]);


        $this->addForeignKey('{{%fk_message_client}}', '{{%viber_message}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%viber_message}}');

        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180204_032542_messages cannot be reverted.\n";

        return false;
    }
    */
}
