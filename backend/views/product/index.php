<?php

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'image',
                'contentOptions' => [
                    'style' => 'width: 160px',
                ],
                'content' => function ($model) {
                    /**
                     * @var \common\models\Product $model
                     */
                    return Html::img($model->getImageUrl(), ['style' => 'width:85px']);
                }
            ],
            [
                'attribute' => 'name',
                'contentOptions' => [
                    'style' => 'width: 160px'
                ]
            ],
            'price:currency',
            [
                'attribute' => 'created_at',
                'format' => ['datetime'],
                'contentOptions' => [
                    'style' => 'white-space: nowrap'
                ]
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime'],
                'contentOptions' => [
                    'style' => 'white-space: nowrap'
                ]
            ],
            [
                'attribute' => 'status',
                'content' => function ($model) {
                    return Html::tag('span', $model->status ? 'Active' : 'Inactive', [
                        'class' => $model->status ? 'badge badge-success' : 'badge badge-warning'
                    ]);
                }
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
</div>