<?php

/**
 * @var \common\models\Order $order
 * @var \common\models\OrderAddress $orderAddress
 * @var array $cartItem
 * @var int $productQuantity
 * @var float $totalPrice
 */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;


?>

<script src="https://www.paypal.com/sdk/js?client-id=AUpQt9J_nnsOiRPp4T2IsMEggZQySCcrLY7jv3490JcpjezdElHAqYfHebrt5e4CRot5mdpC5ZyLGrgT&components=buttons&enable-funding=paylater,venmo,card" data-sdk-integration-source="integrationbuilder_sc"></script>



<div class="row">
    <div class="col">
        <?php $form = ActiveForm::begin([
            'id' => 'checkout-form',
        ]); ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>
                </div>

                <?= $form->field($order, 'email')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>Address Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <?= $form->field($orderAddress, 'address') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($orderAddress, 'country') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col md-6">
                        <?= $form->field($orderAddress, 'city') ?>
                    </div>
                    <div class="col md-3">
                        <?= $form->field($orderAddress, 'state') ?>
                    </div>
                    <div class="col md-3">
                        <?= $form->field($orderAddress, 'zipcode') ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><?= $productQuantity ?> Product(s)</td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($totalPrice) ?></td>
                    </tr>
                </table>
                <!-- paypal button -->
                <div id="paypal-button-container"></div>
                <p id="result-message"></p>
            </div>
        </div>
    </div>
</div>

<script>
    function initPayPalButton() {
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal',
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        "amount": {
                            "currency_code": "USD",
                            "value": <?php echo $totalPrice ?>
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                console.log(data, actions);

                return actions.order.capture().then(function(details) {
                    const $form = $('#checkout-form');
                    const data = $form.serializeArray();
                    debugger;
                    data.push({
                        name: 'transactionId',
                        value: details.id
                    });
                    data.push({
                        name: 'status',
                        value: details.status
                    });
                    $.ajax({
                        method: 'POST',
                        url: '<?= Url::to(['/cart/create-order']) ?>',
                        data: data,
                        success: function(res) {
                            alert('Thank you for your business.');
                            window.location.href = '';
                        }
                    });

                });
            },
        }).render('#paypal-button-container');
    }

    initPayPalButton();
</script>