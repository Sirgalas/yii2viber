<?php

use yii\db\Migration;

/**
 * Class m180220_164018_alter_user_coast_to_cost
 */
class m180220_164018_alter_user_coast_to_cost extends Migration
{
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE "public"."user"
          ALTER COLUMN "coast" TYPE decimal(10,2)');
        $this->execute('
          ALTER TABLE "public"."user" RENAME "coast" TO "cost"');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('
          ALTER TABLE "public"."user" RENAME "cost" TO "coast"');

    }
}
