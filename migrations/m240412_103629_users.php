<?php

use yii\db\Migration;

/**
 * Class m240412_103629_users
 */
class m240412_103629_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(100)->notNull(),
            'password' => $this->string(500)->notNull(),
            'password_reset_token' => $this->string(500),
            'last_login_at' => $this->dateTime(),
            'registration_ip' => $this->string(45)->notNull(),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // Add indexes
        $this->createIndex(
            'idx-users-email',
            '{{%users}}',
            'email'
        );

        // Add foreign keys if needed
        // $this->addForeignKey(
        //     'fk-users-created_by',
        //     '{{%users}}',
        //     'created_by',
        //     '{{%user}}',
        //     'id',
        //     'CASCADE'
        // );

        // $this->addForeignKey(
        //     'fk-users-updated_by',
        //     '{{%users}}',
        //     'updated_by',
        //     '{{%user}}',
        //     'id',
        //     'CASCADE'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys if needed
        // $this->dropForeignKey('fk-users-updated_by', '{{%users}}');
        // $this->dropForeignKey('fk-users-created_by', '{{%users}}');

        // Drop indexes
        $this->dropIndex('idx-users-email', '{{%users}}');

        $this->dropTable('{{%users}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240412_103629_users cannot be reverted.\n";

        return false;
    }
    */
}
