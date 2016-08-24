<?php

use c006\checkout\assets\AppCheckoutSession;

$total = c006\cart\assets\AppCartHelpers::getCartTotal();

$session = new AppCheckoutSession();
$session->init();
$coupons = $session->get('coupon', []);
$savings = 0.00;
?>

<style>
    #mini-cart {

        }

    #mini-cart > .table {
        -webkit-border-radius : 3px;
        -moz-border-radius    : 3px;
        border-radius         : 3px;
        border-top: 1px solid #CCCCCC;
        }

    #mini-cart > .table .table-cell {
        vertical-align : middle;
        text-align     : left;
        padding        : 5px;
        }


    #mini-cart > .table > .table-row > .table-cell {
        padding-right : 5px;
        padding-left  : 5px;
        border-bottom : 1px solid #CCCCCC;
        border-right  : 1px dotted #CCCCCC;
        }

    #mini-cart > .table > .table-row > .table-cell:first-of-type {
        border-left : 1px solid #CCCCCC;
        }

    #mini-cart > .table > .table-row > .table-cell:last-of-type {
        border-right : 1px solid #CCCCCC;
        }

    #mini-cart .savings {
        color       : #1c9a19;
        }


</style>


<div id="mini-cart">
    <div class="table">

        <div class="table-row">
            <div class="table-cell">Subtotal</div>
            <div class="table-cell">$<?= number_format($total, 2) ?></div>
        </div>
        <?php if (is_array($coupons) && sizeof($coupons)) : ?>
            <?php foreach ($coupons as $coupon) : ?>
                <?php $total -= $coupon['value'] ?>
                <div class="table-row">
                    <div class="table-cell savings"><?= $coupon['code'] ?></div>
                    <div class="table-cell savings">($<?= number_format($coupon['value'], 2) ?>)</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($savings > 0.00) : ?>
            <?php $total -= $savings ?>
            <div class="table-row">
                <div class="table-cell savings">Savings</div>
                <div class="table-cell savings">-$<?= number_format($savings, 2) ?></div>
            </div>
        <?php endif; ?>


        <?php if (yii::$app->session->get('shipping.shipping')) : ?>
            <?php $shipping = \c006\shipping\assets\AppHelper::getShippingRule(yii::$app->session->get('shipping.shipping')) ?>
            <?php $total += $shipping['flat_rate'] ?>
            <div class="table-row">
                <div class="table-cell">Shipping <?= $shipping['service_name'] ?></div>
                <div class="table-cell">$<?= number_format($shipping['flat_rate'], 2) ?></div>
            </div>
        <?php endif; ?>

        <div class="table-row">
            <div class="table-cell bold">Total</div>
            <div class="table-cell bold">$<?= number_format($total, 2) ?></div>
        </div>
    </div>
</div>






























