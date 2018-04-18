<?php

use yii\db\Migration;

/**
 * Class m180418_023818_alter_balance_set_whatsapp_as_int
 */
class m180418_023818_alter_balance_set_whatsapp_as_int extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute( ' ALTER TABLE "public"."balance" ALTER COLUMN "whatsapp" TYPE int4 USING (whatsapp::integer)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180418_023818_alter_balance_set_whatsapp_as_int cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180418_023818_alter_balance_set_whatsapp_as_int cannot be reverted.\n";

        return false;
    }
    */
}
