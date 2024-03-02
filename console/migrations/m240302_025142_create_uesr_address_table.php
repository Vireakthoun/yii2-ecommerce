<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%uesr_address}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240302_025142_create_uesr_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%uesr_address}}', [
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
            '{{%idx-uesr_address-user_id}}',
            '{{%uesr_address}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-uesr_address-user_id}}',
            '{{%uesr_address}}',
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
            '{{%fk-uesr_address-user_id}}',
            '{{%uesr_address}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-uesr_address-user_id}}',
            '{{%uesr_address}}'
        );

        $this->dropTable('{{%uesr_address}}');
    }
}
