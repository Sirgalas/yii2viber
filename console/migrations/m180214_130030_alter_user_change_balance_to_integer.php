<?php

use yii\db\Migration;

/**
 * Class m180214_130030_alter_user_change_balance_to_integer
 */
class m180214_130030_alter_user_change_balance_to_integer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->execute('
        ALTER TABLE "public"."user"
        ALTER COLUMN "balance" TYPE int4');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('
          ALTER TABLE "public"."user"
          ALTER COLUMN "balance" TYPE decimal(32,2)');

        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180214_130030_alter_user_change_balance_to_integer cannot be reverted.\n";

        return false;
    }
    */
}
