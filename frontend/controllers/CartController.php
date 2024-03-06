<?php

namespace frontend\controllers;

use common\models\CardItem;
use common\models\Product;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends \frontend\base\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                ]
            ]
        ];
    }

    public function actionIndex()
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

            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            $found = false;

            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] == $id) {
                    $cartItem['quantity']++;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cartItem = [
                    'id' => $id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price
                ];
                $cartItems[] = $cartItem;
            }
            \Yii::$app->session->set(CardItem::SESSION_KEY, $cartItems);
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

    public function actionDelete($id)
    {
        if (isGuest()) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            foreach ($cartItems as $key => $cartItem) {
                if ($cartItem['id'] == $id) {
                    array_splice($cartItems, $key, 1);
                    break;
                }
            }
            \Yii::$app->session->set(CardItem::SESSION_KEY, $cartItems);
        } else {
            CardItem::deleteAll(['product_id' => $id, 'user_id' => currUserId()]);
        }



        return $this->redirect('index');
    }
}
