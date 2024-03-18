<?php

namespace common\models;

use Yii;
use yii\web\Session;

/**
 * This is the model class for table "card_item".
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $quantity
 * @property int|null $user_id
 */
class CardItem extends \yii\db\ActiveRecord
{

    const SESSION_KEY = 'CART_ITEMS';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%card_item}}';
    }

    public static function clearCartItem($currUserId)
    {
        if (isGuest()) {
            Yii::$app->session->remove(CardItem::SESSION_KEY);
        } else {
            CardItem::deleteAll(['user_id' => $currUserId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'user_id' => 'User ID',
        ];
    }

    /**
     * get query for [[Product]]
     * @return \yii\db\ActiveQuery|\common\models\query\ProductQuery
     */

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CardItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CardItemQuery(get_called_class());
    }

    public static function getTotalQuantity($currUserId)
    {
        if (isGuest()) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = CardItem::findBySql(
                "SELECT SUM(quantity) FROM card_item WHERE user_id = :userId",
                ['userId' =>  $currUserId]
            )->scalar();
        }

        return $sum;
    }


    public static function getTotalPrice($currUserId)
    {
        if (isGuest()) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'] * $cartItem['price'];
            }
        } else {
            $sum = CardItem::findBySql(
                "SELECT SUM(c.quantity* p.price) 
                FROM card_item c 
                LEFT JOIN product p on p.id = c.product_id 
                WHERE c.user_id = :userId",
                ['userId' =>  $currUserId]
            )->scalar();
        }

        return $sum;
    }


    public static function getItemForUser($currUserId)
    {
        if (\Yii::$app->user->isGuest) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
        } else {
            $cartItems = CardItem::findBySql(
                '
                   SELECT 
                    c.product_id as id, 
                    p.image, 
                    p.`name`,
                    p.price,
                    c.quantity,
                    p.price*c.quantity as total_price 
                    FROM card_item c
                    LEFT JOIN product p on p.id = c.product_id
                    WHERE c.user_id = :userId
                ',
                ['userId' => $currUserId]
            )->asArray()->all();
        }

        return $cartItems;
    }
}
