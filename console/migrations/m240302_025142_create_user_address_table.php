<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_address}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240302_025142_create_user_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_address}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'address' => $this->string(255)->notNull(),
            'city' => $this->string(100)->notNull(),
            'state' => $this->string(100)->notNull(),
            'country' => $this->string(100)->notNull(),
            'zipcode' => $this->string(100),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_address-user_id}}',
            '{{%user_address}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_address-user_id}}',
            '{{%user_address}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_address-user_id}}',
            '{{%user_address}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_address-user_id}}',
            '{{%user_address}}'
        );

        $this->dropTable('{{%user_address}}');
    }
}
