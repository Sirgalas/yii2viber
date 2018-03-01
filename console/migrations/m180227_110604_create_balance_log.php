<?php

use yii\db\Migration;

/**
 * Class m180227_110604_create_balance_log
 */
class m180227_110604_create_balance_log extends Migration
{


    public function safeUp()
    {
        $this->createTable('{{%balance_log}}',[
            'id'=>$this->primaryKey(),
            'user_id'=>$this->integer()->notNull(),
            'old_balance'=>$this->string(),
            'new_balance'=>$this->string(),
            'diff_balance'=>$this->string(),
            'controller_id'=>$this->string(),
            'action_id'=>$this->string(),
            'type'=>$this->string(),
            'fixed'=>$this->string(),
            'query'=>$this->string(),
            'post'=>$this->string(),
            'created_at'=>$this->dateTime(),


        ]);
        $this->addForeignKey('{{%fk_balance_log_user}}', '{{%balance_log}}', 'user_id', '{{%user}}', 'id');
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%balance_log}}');

        return false;
    }
}
