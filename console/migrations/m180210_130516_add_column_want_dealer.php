<?php

use yii\db\Migration;

/**
 * Class m180210_130516_add_column_want_dealer
 */
class m180210_130516_add_column_want_dealer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user','want_dealer',$this->integer(2));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('user','want_dealer');
    }

}
