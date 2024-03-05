<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <?php echo \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => 'product_item',
            'layout' => '<div class="row mb-3">{items}</div>{summary}{pager}',
            'itemOptions' => [
                'class' => 'col-lg-4 col-md-6 md-4 mb-4 product-item
                '
            ],
            'pager' => [
                'class' => \yii\bootstrap4\LinkPager::class,
                'options' => ['class' => 'pagination justify-content-end'],
                'prevPageLabel' => 'Previous', // Label for the "previous" page button
                'nextPageLabel' => 'Next', // Label for the "next" page button
            ]
        ]) ?>
    </div>
</div>