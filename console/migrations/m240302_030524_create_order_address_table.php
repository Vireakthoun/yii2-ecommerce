<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_address}}`.
 */
class m240302_030524_create_order_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_address}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(11),
            'address' => $this->string(255)->notNull(),
            'city' => $this->string(100)->notNull(),
            'state' => $this->string(100)->notNull(),
            'country' => $this->string(100)->notNull(),
            'zipcode' => $this->string(100),
        ]);

        $this->createIndex(
            '{{%idx-order_address-order_id}}',
            '{{%order_address}}',
            'order_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-order_address-order_id}}',
            '{{%order_address}}',
            'order_id',
            '{{%order}}',
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
            '{{%fk-order_address-order_id}}',
            '{{%order_address}}'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            '{{%idx-order_address-order_id}}',
            '{{%order_address}}'
        );
        $this->dropTable('{{%order_address}}');
    }
}
