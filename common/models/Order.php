<?php

namespace common\models;

use Exception;
use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property float $total_price
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $transaction_id
 * @property int|null $create_at
 * @property int|null $created_by
 * @property int $status
 */
class Order extends \yii\db\ActiveRecord
{

    const STATUS_DRAFT = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_FAILURED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'firstname', 'lastname', 'email', 'status'], 'required'],
            [['total_price'], 'number'],
            [['create_at', 'created_by', 'status'], 'integer'],
            [['firstname', 'lastname'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 255],
            [['transaction_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'create_at' => 'Create At',
            'created_by' => 'Created By',
            'status' => 'Status',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }


    public function saveAddress($postData)
    {
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $this->id;
        if ($orderAddress->load($postData) && $orderAddress->save()) {
            return true;
        }
        throw new Exception('Could not save order address: '
            . implode('<br>', $orderAddress->getFirstErrors()));
    }


    public function saveOrderItems()
    {
        $cartItems = CardItem::getItemForUser(currUserId());
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $this->id;
            $orderItem->quantity = $cartItem['quantity'];

            if (!$orderItem->save()) {
                throw new Exception("Order item was not saved: " . implode('<br>', $orderItem->getFirstErrors()));
            }
        }

        return true;
    }
}
