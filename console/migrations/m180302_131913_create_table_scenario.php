<?php

use yii\db\Migration;

/**
 * Class m180302_131913_create_table_scenario
 */
class m180302_131913_create_table_scenario extends Migration
{   public function safeUp()
{
    $this->createTable('{{%scenario}}',[
        'id'=>$this->primaryKey(),
        'provider'=>$this->string()->notNull(),
        'name'=>$this->string(),
        'from1'=>$this->string(),
        'channel1'=>$this->string(),
        'from2'=>$this->string(),
        'channel2'=>$this->string(),
        'from3'=>$this->string(),
        'channel3'=>$this->string(),
        'default'=>$this->boolean()->defaultValue(false),
        'provider_scenario_id'=>$this->string(),
        'created_at'=>$this->dateTime(),
    ]);
    $this->execute('
        ALTER TABLE "public"."viber_message"
        ALTER COLUMN "scenario_id" TYPE integer
    ');
}


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('
           ALTER TABLE "public"."viber_message"
           DROP COLUMN "scenario_id"
        ');
        $this->dropTable('{{%scenario}}');

        return false;
    }
}
