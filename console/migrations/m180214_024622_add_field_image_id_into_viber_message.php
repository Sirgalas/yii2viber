<?php

use yii\db\Migration;

/**
 * Class m180214_024622_add_field_image_id_into_viber_message
 */
class m180214_024622_add_field_image_id_into_viber_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%viber_message}}', 'viber_image_id',$this->integer()->after('image'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_message}}', 'viber_image_id');

        return false;
    }


}
