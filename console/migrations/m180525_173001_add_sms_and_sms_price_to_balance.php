<?php

use yii\db\Migration;

/**
 * Class m180525_173001_add_sms_and_sms_price_to_balance
 */
class m180525_173001_add_sms_and_sms_price_to_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance','sms',$this->integer());
        $this->addColumn('balance','sms_price',$this->integer());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('balance','sms');
        $this->dropColumn('balance','sms_price');
    }

}
