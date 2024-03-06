<?php

namespace frontend\base;

use common\models\CardItem;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $this->view->params['cartItemCount'] = CardItem::getTotalQuantity(currUserId());
        return parent::beforeAction($action);
    }
}
