<?php

namespace frontend\controllers;

use common\models\CardItem;
use common\models\Product;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends \frontend\base\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'only' => ['add']
            ]
        ];
    }

    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            //get the items\from session
        } else {
            $cartItems = CardItem::findBySql(
                '
                   SELECT 
                    c.product_id, 
                    p.image, 
                    p.`name`,
                    p.price,
                    c.quantity,
                    p.price*c.quantity as amount 
                    FROM card_item c
                    LEFT JOIN product p on p.id = c.product_id
                    WHERE c.user_id = :userId
                ',
                ['userId' => \Yii::$app->user->id]
            )->asArray()->all();
        }

        return $this->render('index', [
            'items' => $cartItems
        ]);
    }

    public function actionAdd()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException('Product does not exist');
        }
        if (\Yii::$app->user->isGuest) {
            //todo Save to session 
        } else {
            $userId = \Yii::$app->user->id;
            $cartItem = CardItem::find()->userId($userId)->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem = new CardItem();
                $cartItem->product_id = $id;
                $cartItem->user_id = $userId;
                $cartItem->quantity = 1;
            }
            if ($cartItem->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $cartItem->errors
                ];
            }
        }
    }
}
