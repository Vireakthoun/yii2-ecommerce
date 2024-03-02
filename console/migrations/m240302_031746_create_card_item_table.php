<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%card_item}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240302_031746_create_card_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%card_item}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11),
            'quantity' => $this->integer(11),
            'user_id' => $this->integer(11),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-card_item-user_id}}',
            '{{%card_item}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-card_item-user_id}}',
            '{{%card_item}}',
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
            '{{%fk-card_item-user_id}}',
            '{{%card_item}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-card_item-user_id}}',
            '{{%card_item}}'
        );

        $this->dropTable('{{%card_item}}');
    }
}
