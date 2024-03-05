<?php

/** @var array $items */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="card">
    <div class="card-header">
        <h3>Your cart items</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td>
                            <img src="<?= \common\models\Product::formatImageUrl($item['image']) ?>" alt="<?= $item['name'] ?>" style="height:75px; object-fit:cover;">
                        </td>
                        <td><?= $item['price'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['amount'] ?></td>
                        <td>
                            <?php echo Html::a('Delete', ['/cart/delete', 'id' => $item['product_id']], [
                                'class' => 'btn btn-outline-danger btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you want to remove this product from cart?'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-body text-right">
        <a href="<?= Url::to(['/cart/checkout']) ?>" class="btn btn-primary">Checkout</a>
    </div>
</div>