<?php

/** @var \common\models\Product $model */

use yii\helpers\Url;

?>

<div class="card h-100">
    <!-- Sale badge-->
    <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">
        Sale
    </div>
    <!-- Product image-->
    <img class="card-img-top" src="<?= $model->getImageUrl() ?>" style="height:320px; object-fit:cover" alt="..." />
    <!-- Product details-->
    <div class="card-body p-4">
        <!-- Product name-->
        <h5 class="fw-bolder"><?php echo $model->name ?></h5>
        <!-- Product reviews-->
        <div class="d-flex small text-warning mb-2">
            <div class="bi-star-fill"></div>
            <div class="bi-star-fill"></div>
            <div class="bi-star-fill"></div>
            <div class="bi-star-fill"></div>
            <div class="bi-star-fill"></div>
        </div>
        <!-- Product price-->
        <h5>
            <?php echo Yii::$app->formatter->asCurrency($model->price); ?>
        </h5>
        <div class="cart-text">
            <?php echo $model->getShortDescription() ?>
        </div>
    </div>
    <!-- Product actions-->
    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
        <a href="<?= Url::to('/cart/add') ?>" class="btn btn-success btn-block btn-add-to-cart">Add to cart</a>
    </div>
</div>