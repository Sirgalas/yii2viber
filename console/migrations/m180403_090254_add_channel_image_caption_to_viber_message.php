<?php

use yii\db\Migration;

/**
 * Class m180403_090254_add_channel_image_caption_to_viber_message
 */
class m180403_090254_add_channel_image_caption_to_viber_message extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."viber_message"
 
ADD COLUMN "image_caption" varchar(255),
ADD COLUMN "channel" varchar(20) DEFAULT 'viber' NOT NULL;');


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE "public"."user" ALTER COLUMN "cost" TYPE integer;');

    }

}
