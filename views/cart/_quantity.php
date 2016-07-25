<?php
/** @var $id int */
/** @var $quantity int */
?>
<style>
    .quantity-container {
        display: block;
        text-align: left;
        margin-bottom: 20px;
    }

    .quantity-container > .table {
        width: auto;
        white-space: nowrap;

    }

    .quantity-container #cart-quantity {
        display: inline-block;
        margin: 0;
        padding-top: 3px;
        padding-right: 8px;
        padding-left: 10px;
        padding-bottom: 6px;
        width: 3em;
        font-size: 1.1em;
        font-weight: 400;
        color: #666666;
        text-align: right;
        border: 1px solid #CCCCCC;
        border-left: 0;
        border-right: 0;
    }

    .quantity-container .input-label {
        display: inline-block;
        background: none;
        position: absolute;
        left: 3px;
        top: 8px;
        color: #999999;
        font-size: 1em;
        font-weight: 400;
    }

    .quantity-container .increment {
        display: inline-block;
        margin: 0;
        padding: 0;
        width: 1.2em;
        font-size: 1.6em;
        font-weight: 500;
        color: #7b7b7b;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid #CCCCCC;
    }

    .quantity-container span.up {
        -webkit-border-top-left-radius: 4px;
        -webkit-border-bottom-left-radius: 4px;
        -moz-border-radius-topleft: 4px;
        -moz-border-radius-bottomleft: 4px;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .quantity-container span.down {
        -webkit-border-top-right-radius: 4px;
        -webkit-border-bottom-right-radius: 4px;
        -moz-border-radius-topright: 4px;
        -moz-border-radius-bottomright: 4px;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .quantity-container span.up:hover,
    .quantity-container span.down:hover {
        background-color: #ebebeb;
    }

    .table-qty {
        position: relative;
        top: 10px;
    }

</style>

<div id="quantity-container-<?= $id ?>" class="table quantity-container">
    <div class="table-cell">
        <div class="table table-qty">
            <div class="table-cell vertical-align-top"><span class="increment up">+</span></div>
            <div class="relative table-cell vertical-align-top"><input type="text" id="cart-quantity" name="Cart[<?= $id ?>][quantity]" value="<?= $quantity ?>"/><span class="input-label">Qty</span>
            </div>
            <div class="table-cell vertical-align-top"><span class="increment down">-</span></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(function () {
        var $quantity = jQuery('#quantity-container-<?= $id ?>');
        var $quantity_input = $quantity.find('#cart-quantity');
        var $quantity_up = $quantity.find('span.up');
        var $quantity_down = $quantity.find('span.down');

        $quantity_up.click(function () {
            var _val = $quantity_input.val() * 1.00 + 1;
            $quantity_input.val(_val.toString().replace(/[^0-9]/, ''));
        });
        $quantity_down.click(function () {
            var _val = $quantity_input.val() * 1.00 - 1;
            _val = (_val < 1) ? 1 : _val;
            $quantity_input.val(_val.toString().replace(/[^0-9]/, ''));
        });
    });
</script>