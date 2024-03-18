<?php

namespace frontend\controllers;

use BadMethodCallException;
use common\models\CardItem;
use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use Yii;
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
                'only' => ['add', 'create-order'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                    'create-order' => ['POST'],
                ]
            ]
        ];
    }

    public function actionIndex()
    {

        $cartItems = CardItem::getItemForUser(currUserId());

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

            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
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

    public function actionChangeQuantity()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException('Product does not exist');
        }

        $quantity = \Yii::$app->request->post('quantity');
        if (isGuest()) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] === $id) {
                    $cartItem['quantity'] = $quantity;
                    break;
                }
            }
            \Yii::$app->session->set(CardItem::SESSION_KEY, $cartItems);
        } else {
            $cartItem = CardItem::find()->userId(currUserId())->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }

        return CardItem::getTotalQuantity(currUserId());
    }


    public function actionCheckout()
    {
        $cartItems = CardItem::getItemForUser(currUserId());
        if (empty($cartItems)) {
            return $this->redirect([Yii::$app->homeUrl]);
        }

        $order = new Order();
        $orderAddress = new OrderAddress();

        if (!isGuest()) {

            /** @var \common\models\User $user */
            $user = Yii::$app->user->identity;
            $userAddress =  $user->getAddress();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_DRAFT;

            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;
        }
        $productQuantity = CardItem::getTotalQuantity(currUserId());
        $totalPrice = CardItem::getTotalPrice(currUserId());

        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

    public function actionCreateOrder()
    {
        $transactionId = Yii::$app->request->post('transactionId');
        $status = Yii::$app->request->post('status');

        $totalPrice = CardItem::getTotalPrice(currUserId());

        if ($totalPrice == null) {
            throw new BadMethodCallException();
        }

        $order = new Order();
        $order->transaction_id = $transactionId;
        $order->total_price = $totalPrice;
        $order->status = $status === 'COMPLETED' ? Order::STATUS_COMPLETED : Order::STATUS_FAILURED;
        $order->create_at = time();
        $order->created_by = currUserId();

        $transaction = Yii::$app->db->beginTransaction();
        $orderAddress = new OrderAddress();
        if (($order->load(Yii::$app->request->post()) && $order->save())
            && $order->save()
            && $order->saveAddress(Yii::$app->request->post())
            && $order->saveOrderItems()
        ) {
            $transaction->commit();

            CardItem::clearCartItem(currUserId());

            return [
                'success' => true
            ];
        } else {
            $transaction->rollBack();
            return [
                'success' => false,
                'errors' => $order->errors
            ];
        }
    }
}
