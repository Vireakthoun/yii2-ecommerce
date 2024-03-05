<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \common\models\UserAddress $userAddress
 */
?>

<?php if (isset($success) && $success) : ?>

    <div class="alert alert-success">
        Your address was successfully updated.
    </div>

<?php endif ?>

<?php $addressForm = ActiveForm::begin([
    'action' => ['/profile/update-address'],
    'options' => [
        'data-pjax' => 1
    ]
]); ?>
<div class="row">
    <div class="col-md-6">
        <?= $addressForm->field($userAddress, 'address') ?>
    </div>
    <div class="col-md-6">
        <?= $addressForm->field($userAddress, 'country') ?>
    </div>
</div>

<div class="row">
    <div class="col md-6">
        <?= $addressForm->field($userAddress, 'city') ?>
    </div>
    <div class="col md-3">
        <?= $addressForm->field($userAddress, 'state') ?>
    </div>
    <div class="col md-3">
        <?= $addressForm->field($userAddress, 'zipcode') ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Add address', ['class' => 'btn btn-primary', 'name' => 'add-address-button']) ?>
</div>
<?php ActiveForm::end() ?>