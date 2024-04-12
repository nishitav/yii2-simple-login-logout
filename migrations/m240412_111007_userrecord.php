<?php

use yii\db\Migration;

/**
 * Class m240412_111007_userrecord
 */
class m240412_111007_userrecord extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('users', [
            'id' => 1,
            'email' => 'vadhwananishit@gmail.com',
            'password' => '$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a', // text is rasmuslerdorf
            'password_reset_token' => null,
            'last_login_at' => '2024-04-12 08:00:00',
            'registration_ip' => '127.0.0.1',
            'is_active' => 1,
            'created_at' => '2024-04-12 08:00:00',
            'updated_at' => '2024-04-12 08:00:00',
            'created_by' => null,
            'updated_by' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // You can revert the insertion, but since it's a one-time migration for adding initial data,
        // it's not necessary to define a down() method.
        // $this->delete('users', ['id' => 1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240412_111007_userrecord cannot be reverted.\n";

        return false;
    }
    */
}
