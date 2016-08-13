<?php
use c006\checkout\assets\AppCheckoutSession;
use c006\products\assets\ModelHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $items array */
/** @var $session array */

/** @var $is_summary boolean */
$is_summary = (!isset($is_summary)) ? FALSE : $is_summary;

/** @var $total float */
$total = 0.00;

/** @var $savings float */
$savings = 0.00;

$session = new AppCheckoutSession();
$session->init();
$coupons = $session->get('coupon', []);
?>

    <style>
        #cart-items {
            display: block;
            margin-top: 30px;
        }

        #cart-items > .table {
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
        }

        #cart-items > .table .table-cell {
            vertical-align: middle;
            text-align: left;
            padding: 5px;
        }

        #cart-items .title {
            font-size: 1.1em;
            font-weight: 500;
        }

        #cart-items .savings {
            font-size: 1.1em;
            font-weight: 500;
            color: #9b403d;
        }

        #cart-items .image .small-image {
            padding-top: 2px;
            height: 50px;
        }

        #cart-items .table-row:first-of-type .table-cell {
            border-top: 1px solid #CCCCCC;
        }

        #cart-items > .table > .table-row:hover > .table-cell {
            background-color: rgba(204, 204, 204, 0.30);
        }

        #cart-items > .table > .table-row > .table-cell {
            padding-right: 5px;
            padding-left: 5px;
            border-bottom: 1px solid #CCCCCC;
            border-right: 1px dotted #CCCCCC;
        }

        #cart-items > .table > .table-row > .table-cell:first-of-type {
            border-left: 1px solid #CCCCCC;
        }

        #cart-items > .table > .table-row > .table-cell:last-of-type {
            border-right: 1px solid #CCCCCC;
        }

        #cart-items .table-cell.discount {
            color: #80aa88;
        }

        .boarder-right-0 {
            border-right: 0 !important;
        }
    </style>

<?php $form = ActiveForm::begin([
    'action' => 'cart/update',
]); ?>
    <div id="cart-items">

        <div class="table">
            <div class="table-row">
                <div class="table-cell align-center title"></div>
                <div class="table-cell align-right title">Model</div>
                <div class="table-cell align-right title">Name</div>
                <div class="table-cell align-right title">Qty</div>
                <div class="table-cell align-right title">Price</div>
                <div class="table-cell align-right title">Discount</div>
                <div class="table-cell align-right title">Auto Ship</div>
                <div class="table-cell align-right title">Totals</div>
                <?php if ($is_summary == FALSE) : ?>
                    <div class="table-cell align-right title"></div>
                <?php endif ?>
            </div>
            <?php foreach ($items as $item) : ?>
                <?php $total += $item['quantity'] * $item['price']; ?>
                <?php $savings += $item['quantity'] * $item['price'] * ($item['discount'] / 100); ?>
                <?php $auto_ship = ModelHelper::getAutoShipLinkName($item['auto_ship']) ?>
                <div class="table-row">
                    <div class="table-cell align-center image">
                        <?php if ($is_summary) : ?>
                            <img class="small-image" src="/images/products/<?= $item['image'] ?>?<?= time() ?>" alt="<?= $item['model'] ?>">
                        <?php else : ?>
                            <a href="<?= $item['url'] ?>"><img class="small-image" src="/images/products/<?= $item['image'] ?>?<?= time() ?>" alt="<?= $item['model'] ?>"></a>
                        <?php endif ?>
                    </div>
                    <div class="table-cell align-right model"><?= $item['model'] ?></div>
                    <div class="table-cell align-right name">
                        <?php if ($is_summary) : ?>
                            <?= $item['name'] ?>
                        <?php else : ?>
                            <a href="<?= $item['url'] ?>"><?= $item['name'] ?></a>
                        <?php endif ?>
                    </div>
                    <div class="table-cell align-right quantity">
                        <?php if ($is_summary) : ?>
                            <?= $item['quantity'] ?>
                        <?php else : ?>
                            <?= yii\base\View::render('_quantity', ['id' => $item['id'], 'quantity' => $item['quantity']]) ?>
                        <?php endif ?>
                    </div>
                    <div class="table-cell align-right price"><?= $item['price'] ?></div>
                    <div class="table-cell align-right discount"><?= ($item['discount'] + 0.00 > 0.00) ? $item['discount'] . '% Off' : '' ?></div>
                    <div class="table-cell align-right auto-ship"><?= (sizeof($auto_ship)) ? $auto_ship['duration'] . ' ' . $auto_ship['type'] : '' ?></div>
                    <div class="table-cell align-right totals">
                        $<?= number_format($item['quantity'] * $item['price'], 2) ?>
                        <input type="hidden" name="Cart[<?= $item['id'] ?>][product_id]" value="<?= $item['product_id'] ?>"/>
                    </div>
                    <?php if ($is_summary == FALSE) : ?>
                        <div class="table-cell align-right"><a href="cart/delete?id=<?= $item['id'] ?>"><span class="icon icon-inverse-opacity icon-delete vertical-align-middle"></span></a></div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>

            <?php if (is_array($coupons) && sizeof($coupons)) : ?>
                <?php foreach ($coupons as $coupon) : ?>
                    <?php $total -= $coupon['value'] ?>
                    <div class="table-row">
                        <div class="table-cell align-center boarder-right-0"></div>
                        <div class="table-cell align-right  boarder-right-0"></div>
                        <div class="table-cell align-right  boarder-right-0"></div>
                        <div class="table-cell align-right  boarder-right-0"></div>
                        <div class="table-cell align-right  boarder-right-0"></div>
                        <div class="table-cell align-right  boarder-right-0"></div>
                        <div class="table-cell align-right  savings title"><?= $coupon['code'] ?></div>
                        <div class="table-cell align-right savings title padding-10 title-medium">$<?= number_format($coupon['value'], 2) ?></div>
                        <?php if ($is_summary == FALSE) : ?>
                            <div class="table-cell align-right title"></div>
                        <?php endif ?>
                    </div>
                <?php endforeach; ?>
            <?php endif ?>

            <?php if ($savings > 0.00) : ?>
                <?php $total -= $savings ?>
                <div class="table-row">
                    <div class="table-cell align-center boarder-right-0"></div>
                    <div class="table-cell align-right  boarder-right-0"></div>
                    <div class="table-cell align-right  boarder-right-0"></div>
                    <div class="table-cell align-right  boarder-right-0"></div>
                    <div class="table-cell align-right  boarder-right-0"></div>
                    <div class="table-cell align-right  boarder-right-0"></div>
                    <div class="table-cell align-right  savings title">Savings</div>
                    <div class="table-cell align-right savings title padding-10 title-medium">$<?= number_format($savings, 2) ?></div>
                    <?php if ($is_summary == FALSE) : ?>
                        <div class="table-cell align-right title"></div>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <div class="table-row">
                <div class="table-cell align-center boarder-right-0"></div>
                <div class="table-cell align-right  boarder-right-0"></div>
                <div class="table-cell align-right  boarder-right-0"></div>
                <div class="table-cell align-right  boarder-right-0"></div>
                <div class="table-cell align-right  boarder-right-0"></div>
                <div class="table-cell align-right  boarder-right-0"></div>
                <div class="table-cell align-right  title">Total</div>
                <div class="table-cell align-right title padding-10 title-medium">$<?= number_format(($total > 0) ? $total : 0, 2) ?></div>
                <?php if ($is_summary == FALSE) : ?>
                    <div class="table-cell align-right title"></div>
                <?php endif ?>
            </div>


        </div>
        <?php if ($is_summary == FALSE) : ?>
            <div class="padding-top-10 form-group">
                <?= Html::submitButton('Empty Cart', ['class' => 'btn btn-primary', 'onclick' => 'document.forms[0].action=\'cart/empty\'']) ?>
                <?= Html::submitButton('Update Cart', ['class' => 'btn btn-secondary']) ?>
            </div>
        <?php endif ?>
    </div>
<?php ActiveForm::end(); ?>