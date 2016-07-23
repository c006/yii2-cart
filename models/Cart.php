<?php

namespace c006\cart\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property integer $id
 * @property string $session_id
 * @property integer $product_id
 * @property string $image
 * @property string $model
 * @property string $name
 * @property integer $quantity
 * @property string $price
 * @property string $discount
 * @property integer $discount_type_id
 * @property string $auto_ship
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'discount_type_id'], 'integer'],
            [['price', 'discount'], 'number'],
            [['session_id'], 'string', 'max' => 26],
            [['image', 'name'], 'string', 'max' => 100],
            [['model'], 'string', 'max' => 20],
            [['auto_ship'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'session_id' => Yii::t('app', 'Session ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'image' => Yii::t('app', 'Image'),
            'model' => Yii::t('app', 'Model'),
            'name' => Yii::t('app', 'Name'),
            'quantity' => Yii::t('app', 'Qty'),
            'price' => Yii::t('app', 'Price'),
            'discount' => Yii::t('app', 'Discount'),
            'discount_type_id' => Yii::t('app', 'Discount Type ID'),
            'auto_ship' => Yii::t('app', 'Auto Ship'),
        ];
    }
}
