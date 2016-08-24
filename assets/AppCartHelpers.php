<?php
namespace c006\cart\assets;

use c006\cart\models\Cart;
use c006\products\assets\ModelHelper;
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
            ->leftJoin('product_value_url', 'product_value_url.product_id = cart.product_id')
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
        if (!session_id()) {
            session_start();
        }
        $model = Cart::find()
            ->select(" SUM(quantity * price) AS `total` ")
            ->where(['session_id' => session_id()])
            ->asArray()
            ->one();

        return (float)(sizeof($model)) ? $model['total'] : 0.00;
    }

    /**
     * @return bool
     */
    static public function requiresShipping()
    {
        foreach (self::getCartItems() as $item) {
            if ($item['shipping_id']) {
                return TRUE;
            }
        }

        return FALSE;
    }


    static public function addCartItem($post)
    {
        $cart = [];
        $cart['product_id'] = $post['id'];
        $cart['model'] = $post['core_sku'];
        $cart['name'] = $post['core_name'];
        $cart['quantity'] = $post['qty'];
        $cart['auto_ship'] = (isset($post['auto_ship'])) ? $post['auto_ship'] : 0;
        $cart['price'] = $post['core_price'];
        $cart['discount'] = $post['core_discount'] * 1.00;
        $cart['discount_type_id'] = $post['core_discount_type'];

        $quantity = self::checkCartItemExists([
            'product_id'       => $cart['product_id'],
            'auto_ship'        => $cart['auto_ship'],
            'discount_type_id' => $cart['discount_type_id'],
        ]);

        if ($quantity) {
            $cart['quantity'] += $quantity;
        }

        ModelHelper::saveModelForm('c006\cart\models\Cart', $cart);

    }


    static private function checkCartItemExists($array)
    {
        foreach (self::getCartItems() as $item) {
            $check = 0;
            foreach ($array as $k => $v) {
                echo($item[$k] . ' :: ' . $v);
                if ($item[$k] == $v) {
                    $check++;
                }
            }
            if ($check == sizeof($array)) {
                return $item['quantity'];
            }
        }

        return 0;
    }


    /**
     * @param $old
     * @param $new
     */
    static public function updateSessionId($old, $new)
    {
        $connection = Yii::$app->getDb();
        $sql = "UPDATE `cart` SET `session_id` = '" . $new . "' WHERE `session_id` = '" . $old . "' ";
        $command = $connection->createCommand($sql);
        $command->query();
    }

    /**
     *
     */
    static public function destroyCart()
    {
        Cart::deleteAll(" session_id = '" . Yii::$app->session->id . "'");
    }

}