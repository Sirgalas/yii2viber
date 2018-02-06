<?php

use yii\db\Migration;

/**
 * Class m180206_020615_alter_viber_message
 */
class m180206_020615_alter_viber_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->dropColumn('{{%viber_message}}', 'user_id');
        $this->dropColumn('{{%viber_message}}', 'title');
        $this->dropColumn('{{%viber_message}}', 'text');
        $this->dropColumn('{{%viber_message}}', 'image');
        $this->dropColumn('{{%viber_message}}', 'title_button');
        $this->dropColumn('{{%viber_message}}', 'url_button');

        $this->addColumn('{{%viber_message}}', 'user_id',$this->integer()->after('id'));
        $this->addColumn('{{%viber_message}}', 'title',$this->string(50)->after('user_id'));
        $this->addColumn('{{%viber_message}}', 'text',$this->string(120)->after('user_id'));
        $this->addColumn('{{%viber_message}}', 'image',$this->string(255)->after('user_id'));
        $this->addColumn('{{%viber_message}}', 'title_button',$this->string(32)->after('user_id'));
        $this->addColumn('{{%viber_message}}', 'url_button',$this->string(255)->after('user_id'));


        $this->addForeignKey('{{%fk_message_client}}', '{{%viber_message}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%viber_message}}', 'user_id', $this->intger()->null());
        $this->alterColumn('{{%viber_message}}', 'title', $this->notNull());
        $this->alterColumn('{{%viber_message}}', 'text', $this->notNull());
        $this->alterColumn('{{%viber_message}}', 'image', $this->notNull());
        $this->alterColumn('{{%viber_message}}', 'title_button', $this->notNull());
        $this->alterColumn('{{%viber_message}}', 'url_button', $this->notNull());

        return false;
    }

}
