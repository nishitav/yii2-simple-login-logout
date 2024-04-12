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
            'email' => 'user@example.com',
            'password' => '$2y$13$7Q5uYxNdmG/CqxWnuXZbUOQoWuj7FZ8X0g4qhm6T3dCOKRvtzyOnW', // text is password
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
