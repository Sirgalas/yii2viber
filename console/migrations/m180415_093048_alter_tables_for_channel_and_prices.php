<?php

use yii\db\Migration;

/**
 * Class m180415_093048_alter_tables_for_channel_and_prices
 */
class m180415_093048_alter_tables_for_channel_and_prices extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%balance}}', 'viber_price',$this->string(20));
        //$this->addColumn('{{%balance}}', 'whatsapp',$this->string(20));
        $this->addColumn('{{%balance}}', 'whatsapp_price',$this->string(20));
        $this->addColumn('{{%balance}}', 'telegram_price',$this->string(20));
        $this->addColumn('{{%balance}}', 'wechat_price',$this->string(20));
        //$this->dropColumn('{{%balance}}', 'watsapp');
        $this->addColumn('{{%balance_log}}', 'channel',$this->string(20));


    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%balance_log}}', 'channel');
        $this->dropColumn('{{%balance}}', 'viber_price');
        //$this->dropColumn('{{%balance}}', 'whatsapp');
        $this->dropColumn('{{%balance}}', 'whatsapp_price');
        $this->dropColumn('{{%balance}}', 'telegram_price');
        $this->dropColumn('{{%balance}}', 'wechat_price');
        //$this->addColumn('{{%balance}}', 'watsapp',$this->string(20));
        return false;
    }
}
