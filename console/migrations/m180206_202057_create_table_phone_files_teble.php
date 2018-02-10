<?php

use yii\db\Migration;

/**
 * Class m180206_202057_create_table_phone_files_teble
 */
class m180206_202057_create_table_phone_files_teble extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%files_phone}}',[
            'id'=>$this->primaryKey(),
            'user_id'=>$this->integer()->notNull(),
            'file'=>$this->string(),
            'month'=>$this->integer(),
            'years'=>$this->integer()
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
       $this->dropTable('{{%files_phone}}');
    }


}
