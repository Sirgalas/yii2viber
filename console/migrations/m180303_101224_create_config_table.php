<?php

use yii\db\Migration;

/**
 * Handles the creation of table `config`.
 */
class m180303_101224_create_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('config', [
            'id' => $this->primaryKey(),
            'param'=>$this->string(255),
            'text'=>$this->string(5000),
            'description'=>$this->string(255)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('config');
    }
}
