<?php

use yii\db\Migration;

/**
 * Class m180222_130954_add_date_dend_finish_to_viber_message
 */
class m180222_130954_add_date_dend_finish_to_viber_message extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%viber_message}}', 'date_send_finish', $this->integer()->after('created_at'));
        $this->execute('
        Update viber_message set date_send_finish='.time() .', 
                    dlr_timeout=coalesce(dlr_timeout, 24 * 3600)
        WHERE status=\'wait\'');
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%viber_message}}', 'date_send_finish');

        return false;
    }
}
