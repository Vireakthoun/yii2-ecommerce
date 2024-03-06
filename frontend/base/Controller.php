<?php

namespace frontend\base;

use common\models\CardItem;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            $cartItems = \Yii::$app->session->get(CardItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = CardItem::findBySql(
                "SELECT SUM(quantity) FROM card_item WHERE user_id = :userId",
                ['userId' =>  \Yii::$app->user->id]
            )->scalar();
        }
        $this->view->params['cartItemCount'] = $sum;
        return parent::beforeAction($action);
    }
}
