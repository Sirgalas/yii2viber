<?php

use yii\db\Migration;

/**
 * Class m180415_102918_delete_price_balance_from_user
 */
class m180415_102918_delete_price_balance_from_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql =  "Insert into balance(user_id, viber, viber_price)
                 Select id, balance,cost from \"user\" where id not in (select user_id from balance)";

        $this->dropColumn('{{%user}}', 'cost');
        $this->dropColumn('{{%user}}', 'balance');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%user}}', 'cost',$this->string(20));
        $this->addColumn('{{%user}}', 'balance',$this->integer);

        return false;
    }


}
