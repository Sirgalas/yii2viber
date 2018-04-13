<?php

use yii\db\Migration;

/**
 * Handles the creation of table `balance`.
 */
class m180413_182924_create_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('balance', [
            'id' => $this->primaryKey(),
            'user_id'=>$this->integer()->notNull(),
            'viber'=>$this->integer(),
            'watsapp'=>$this->integer(),
            'telegram'=>$this->integer(),
            'wechat'=>$this->integer()
        ]);

        $this->addForeignKey(
            'fk-balance-user_id',
            'balance',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-balance-user_id',
            'balance'
        );
        $this->dropTable('balance');
    }
}
