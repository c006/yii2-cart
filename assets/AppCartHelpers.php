<?php
namespace c006\cart\assets;

use c006\cart\models\Cart;
use Yii;

class AppCartHelpers
{
    /**
     * @return mixed
     */
    static public function getCartItems()
    {

        return Cart::find()
            ->select('cart.*, alias_url.public AS `url`')
            ->innerJoin('product_value_url', 'product_value_url.product_id = cart.product_id')
            ->leftJoin('alias_url', 'alias_url.id = product_value_url.alias_url_id')
            ->where(['session_id' => Yii::$app->session->id])
            ->asArray()
            ->all();
    }

    /**
     * @return float
     */
    static public function getCartTotal()
    {
        $model = Cart::find()
            ->select("SUM(quantity * price) AS `total`")
            ->where(['session_id' => Yii::$app->session->id])
            ->asArray()
            ->one();

        return (float)$model['total'];

    }

    /**
     *
     */
    static public function destroyCart()
    {
        Cart::deleteAll(" session_id = '" . Yii::$app->session->id . "'");
    }

}