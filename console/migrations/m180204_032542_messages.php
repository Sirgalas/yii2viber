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
            'id'         => $this->primaryKey(),
            'client_id'    => $this->integer()->null(),
            'title'  => $this->string(50)->notNull(),
            'text'  => $this->string(120)->notNull(),
            'image'  => $this->string(255)->notNull(),
            'title_button'  => $this->string(32)->notNull(),
            'url_button'  => $this->string(255)->notNull(),
            'type'=>$this->string(10)->notNull()->defaultValue('text'),
            'alpha_name'=>$this->string(32),
            'date_start'=>$this->string(32),
            'date_finish'=>$this->string(32),
            'time_start'=>$this->string(32),
            'time_finish'=>$this->string(32)
        ]);

        $this->createIndex('{{%ind_message_client_id}}', '{{%message}}', ['client_id'], false);
        $this->addForeignKey('{{%fk_message_client}}', '{{%message}}', 'client_id', '{{%client}}', 'id', $this->cascade, $this->restrict);
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
