<?php

namespace common\models;

use Yii;

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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%card_item}}';
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
}
