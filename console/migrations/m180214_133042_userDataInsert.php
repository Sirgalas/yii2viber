<?php

use yii\db\Migration;

/**
 * Class m180214_133042_userDataInsert
 */
class m180214_133042_userDataInsert extends Migration
{
    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%user}}',
            [  "username", "email", "password_hash", "auth_key",  "confirmed_at", "unconfirmed_email", "blocked_at", "registration_ip", "created_at", "updated_at", "flags", "last_login_at", "type", "dealer_id", "balance"],
            [
                [

                    'username' => 'kev_admin',
                    'email' => 'mkev@gmx.com',
                    'password_hash' => '$2y$10$aSS23bgjBDA5JnkFOo3lFOOiNRGyHH2N1MdwO02Lbe/bl.2KfJd4K',
                    'auth_key' => '1qX8vqTiYJirYOB7vhJqpthuj3x-ScDw',
                    'confirmed_at' => time(),
                    'unconfirmed_email' => null,
                    'blocked_at' => null,
                    'registration_ip' => '127.0.0.1',
                    'created_at' => 1517708141,
                    'updated_at' => 1518153533,
                    'flags' => 0,
                    'last_login_at' => time(),
                    'type' => 'admin',
                    'dealer_id' => 0,
                    'balance' => 1000,

                ],
                [

                    'username' => 'admin2',
                    'email' => 'admin@vibershop24.ru',
                    'password_hash' => '$2y$10$aSS23bgjBDA5JnkFOo3lFOOiNRGyHH2N1MdwO02Lbe/bl.2KfJd4K',
                    'auth_key' => '1qX8vqTiYJirYOB7vhJqpthuj3x-ScDw',
                    'confirmed_at' => time(),
                    'unconfirmed_email' => null,
                    'blocked_at' => null,
                    'registration_ip' => '127.0.0.1',
                    'created_at' => 1517708141,
                    'updated_at' => 1518153533,
                    'flags' => 0,
                    'last_login_at' => time(),
                    'type' => 'admin',
                    'dealer_id' => 0,
                    'balance' => 1000,
                ],
            ]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%user}} CASCADE');
    }

}
