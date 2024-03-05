<?php

namespace frontend\base;

use common\models\CardItem;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $this->view->params['cartItemCount'] = CardItem::findBySql(
            "SELECT SUM(quantity) FROM card_item WHERE user_id = :userId",
            ['userId' =>  \Yii::$app->user->id]
        )->scalar();
        return parent::beforeAction($action);
    }
}
