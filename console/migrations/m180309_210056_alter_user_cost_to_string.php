<?php

use yii\db\Migration;

/**
 * Class m180309_210056_alter_user_cost_to_string
 */
class m180309_210056_alter_user_cost_to_string extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."user" ALTER COLUMN "cost" TYPE varchar(12);');


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE "public"."user" ALTER COLUMN "cost" TYPE integer;');

    }
}
