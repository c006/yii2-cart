<?php
use yii\helpers\Html;

/** @var $model array */
/** @var $session array */

$searchModel  = new \c006\cart\models\search\Cart();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>

<style>
    .grid-view .filters {
        display :none;
    }
</style>

<div id="cart-container">

    <h1 class="title-large">Shopping Cart</h1>

    <?php echo yii\base\View::render('_items', ['items' => $model, 'session'=> $session]) ?>

    <div class="bottom-container">

        <div class="table form-group">
            <div class="table-cell width-30">
                <?php echo yii\base\View::render('@c006/coupon/views/frontend/index', []) ?>
            </div>
            <div class="table-cell width-70"> </div>
        </div>

        <div class="form-group">
            <div class="table">
                <div class="table-cell">
                    <?= Html::a(Yii::t('app', 'Continue Shopping'), '/', ['class' => 'btn btn-success']) ?>
                </div>
                <div class="table-cell align-right">
                    <?= Html::a(Yii::t('app', 'Checkout'), '/checkout', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>




