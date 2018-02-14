<?php

use yii\db\Migration;

/**
 * Class m180213_121423_create_table_viber_transaction
 */
class m180213_121423_create_table_viber_transaction extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {


        $this->execute('
            CREATE TABLE "public"."viber_transaction" (
                "id" int4 DEFAULT nextval(\'user_id_seq\'::regclass) NOT NULL,
                "user_id" int4 NOT NULL,
                "viber_message_id" int4 NOT NULL,
                "status" varchar(60) COLLATE "default" NOT NULL,
                "created_at" int4 NOT NULL,
                "delivered" int4 DEFAULT 0,
                "viewed" int4 DEFAULT 0,
                "phones" jsonb
            )
            WITH (OIDS=FALSE)');
        $this->execute('
          ALTER TABLE "public"."viber_transaction"
          ADD CONSTRAINT "viber_transaction_pkey" PRIMARY KEY ("id")'
        );

        $this->addForeignKey('{{%fk_viber_transaction_user}}', '{{%viber_transaction}}', 'user_id', '{{%user}}', 'id',
            'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%fk_viber_transaction_viber_message}}', '{{%viber_transaction}}', 'viber_message_id',
            '{{%viber_message}}', 'id', 'CASCADE', 'CASCADE');
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%viber_transaction}}');

        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180213_121423_create_table_viber_transaction cannot be reverted.\n";

        return false;
    }
    */
}
