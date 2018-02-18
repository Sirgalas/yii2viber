<?php

use yii\db\Migration;

/**
 * Class m180218_115834_alter_viber_message
 */
class m180218_115834_alter_viber_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE "public"."viber_message"
        ALTER COLUMN "text" TYPE varchar(1024) COLLATE "default",
        ALTER COLUMN "title_button" TYPE varchar(25) COLLATE "default",
        ADD COLUMN "message_type" varchar(32) DEFAULT \'реклама\''
        );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('
        ALTER TABLE "public"."viber_message"
       
        DROP COLUMN "message_type" '
        );

        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180218_115834_alter_viber_message cannot be reverted.\n";

        return false;
    }
    */
}
