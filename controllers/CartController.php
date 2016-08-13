<?php

namespace c006\cart\controllers;

use c006\alerts\Alerts;
use c006\cart\assets\AppCartHelpers;
use c006\cart\models\Cart;
use c006\checkout\assets\AppCheckoutSession;
use c006\core\assets\CoreHelper;
use c006\products\assets\ModelHelper;
use c006\products\assets\ProdHelpers;
use Yii;
use yii\web\Controller;

class CartController extends Controller
{
    private $cart_id = 0;
    private $session;

    function init()
    {
        parent::init();
//        AppAsset::register($this->getView());

        $this->session = new AppCheckoutSession();
        $this->session->init();
    }

    public function actionIndex()
    {
        $model = AppCartHelpers::getCartItems();

//        print_r($model);
//        exit;

        return $this->render('index',
            [
                'model'   => $model,
                'session' => $this->session,
            ]);
    }


    public function actionUpdate()
    {
        if (isset($_POST['Cart'])) {
            foreach ($_POST['Cart'] as $cart_id => $item) {
                if (isset($item['quantity'])) {
                    $cart = self::calculatePrice($_POST['Cart'][$cart_id]['product_id'], $item['quantity']);
                    $cart['id'] = $cart_id;
//                    print_r($cart); exit;
                    ModelHelper::saveModelForm('c006\cart\models\Cart', $cart);
                }
            }

            Alerts::setAlertType(Alerts::ALERT_INFO);
            Alerts::setMessage('The Shopping Cart has been updated.');
            Alerts::setCountdown(5);
        }

        return $this->redirect('/cart');
    }


    public function actionAdd()
    {
        if (isset($_POST['Cart']) && isset($_POST['Cart']['id'])) {
            $this->cart_id = 0;
            $cart = [];
            $post = $_POST['Cart'];
            $cart['product_id'] = $post['id'];
            $model = ProdHelpers::getProduct($cart['product_id']);

            $cart['model'] = $model['core_sku'];
            $cart['name'] = $model['core_name'];
            $cart['quantity'] = $post['qty'];
            $cart['auto_ship'] = (isset($post['auto_ship'])) ? $post['auto_ship'] : 0;
            $cart['price'] = $model['core_price'];
            $cart['discount'] = $model['core_discount'] * 1.00;
            $cart['discount_type_id'] = $model['core_discount_type'];

            $quantity = self::checkCartItemExists([
                'product_id'       => $cart['product_id'],
                'auto_ship'        => $cart['auto_ship'],
                'discount_type_id' => $cart['discount_type_id'],
            ]);
            if ($quantity) {
                $cart['quantity'] += $quantity;
                $cart['id'] = $this->cart_id;
            }

            $cart = self::calculatePrice($cart['product_id'], $cart['quantity']);
            $cart['shipping_id'] = (isset($model['component_shipping_address_id'])) ? $model['component_shipping_address_id'] : 0;

            $image = ProdHelpers::getProductImages($cart['product_id'], 'sml');
            if (sizeof($image)) {
                $cart['image'] = $image[0]['file'];
            }

            $cart['session_id'] = Yii::$app->session->id;
            ModelHelper::saveModelForm('c006\cart\models\Cart', $cart);

            Alerts::setAlertType(Alerts::ALERT_SUCCESS);
            Alerts::setMessage($cart['name'] . ' has been added to the cart');
            Alerts::setCountdown(10);

            return $this->redirect('/cart');
        }
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id', 0);
        Cart::deleteAll("session_id = '" . Yii::$app->session->id . "' AND id = " . $id);

        Alerts::setAlertType(Alerts::ALERT_INFO);
        Alerts::setMessage('Your Shopping Cart has been updated');
        Alerts::setCountdown(5);

        return $this->redirect('/cart');
    }

    public function actionEmpty()
    {
        Cart::deleteAll("session_id = '" . Yii::$app->session->id . "'");

        Alerts::setAlertType(Alerts::ALERT_WARNING);
        Alerts::setMessage('Your Shopping cart has been emptied');
        Alerts::setCountdown(5);

        return $this->redirect('/cart');
    }

    /**
     * @param $product_id
     * @param $quantity
     *
     * @return array
     */
    private function calculatePrice($product_id, $quantity)
    {

        $model = ProdHelpers::getProduct($product_id);
        $cart = [];
        $cart['product_id'] = $product_id;
        $cart['model'] = $model['core_sku'];
        $cart['name'] = $model['core_name'];
        $cart['quantity'] = $quantity;
        $cart['auto_ship'] = (isset($post['auto_ship'])) ? $post['auto_ship'] : "";
        $cart['price'] = $model['core_price'];
        $cart['discount'] = $model['core_discount'] * 1.00;
        $cart['discount_type_id'] = $model['core_discount_type'];

        $price_tier_id = ProdHelpers::getPriceTierId($product_id);
        if ($price_tier_id && $cart['quantity'] > 1) {
            $price_tier_items = ProdHelpers::getPriceTierItems($price_tier_id);
            foreach ($price_tier_items as $item) {
                if ($cart['quantity'] >= $item['max_qty']) {
                    if ($item['is_percentage']) {
                        $cart['price'] = $cart['price'] - ($item['price'] / 100 * $cart['price']);
                        $cart['discount'] = number_format(CoreHelper::getPercentage($cart['price'], $model['core_price']), 2);
                    }
                }
            }
        } else if ($cart['discount'] > 0.00) {
            $discount_type = $model['core_discount_type'];
            if ($discount_type == 3 /* Amount Off */) {
                $cart['discount'] = number_format(100 - CoreHelper::getPercentage($cart['discount'], $cart['price']), 2);
                $cart['price'] = number_format($cart['price'] + 0.00 - $cart['discount'], 2);
            } elseif ($discount_type == 4 /* Percentage Off */) {
                $cart['discount'] = number_format($cart['discount'] + 0.00, 2);
                $cart['price'] = number_format((1 - $cart['discount'] / 100) * $cart['price'], 2);
            } elseif ($discount_type == 5 /* This Amount */) {
                $cart['discount'] = number_format(CoreHelper::getPercentage($cart['discount'], $cart['price']), 2);
                $cart['price'] = number_format($cart['discount'] + 0.00, 2);
            }
        }

        return $cart;

    }

    /**
     * @param $array
     *
     * @return int|mixed
     */
    private function checkCartItemExists($array)
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
                $this->cart_id = $item['id'];

                return $item['quantity'];
            }
        }

        return 0;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    private function getCartItems()
    {
        $model = Cart::find()
            ->where(['session_id' => Yii::$app->session->id])
            ->asArray()
            ->all();

        return $model;
    }

}
