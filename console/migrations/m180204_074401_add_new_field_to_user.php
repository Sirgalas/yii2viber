<?php

use yii\db\Migration;

/**
 * Class m180204_074401_add_new_field_to_user
 */
class m180204_074401_add_new_field_to_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'type',$this->string(8)->after('id'));
        $this->addColumn('{{%user}}', 'dealer_id',$this->integer()->after('id'));
        $this->addColumn('{{%user}}', 'balance',$this->money());
        $this->addColumn('{{%user}}', 'dealer_confirmed',$this->boolean());
        $this->addColumn('{{%user}}', 'image',$this->string(256));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'type');
        $this->dropColumn('{{%user}}', 'dealer_id');
        $this->dropColumn('{{%user}}', 'balance');
        $this->dropColumn('{{%user}}', 'dealer_confirmed');
        $this->dropColumn('{{%user}}', 'image');


        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180204_074401_add_new_field_to_user cannot be reverted.\n";

        return false;
    }
    */
}
