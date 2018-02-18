<?php

use yii\db\Migration;

/**
 * Class m180217_142159_add_collumn_user_coast_and_phone
 */
class m180217_142159_add_collumn_user_coast_and_phone extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
          ALTER TABLE "public"."user"
          ADD COLUMN "coast" money');
        $this->addColumn('user','tel',$this->string(125));
        $this->addColumn('user','time_work',$this->string(255));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
       $this->dropColumn('user','coast');
        $this->dropColumn('user','tel');
        $this->dropColumn('user','time_work');
    }
}
