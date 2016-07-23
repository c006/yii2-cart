<?php

namespace c006\cart\models\search;

use c006\cart\models\Cart as CartModel;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AutoShip represents the model behind the search form about `c006\products\models\AutoShip`.
 */
class Cart extends CartModel
{
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
    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CartModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
// uncomment the following line if you do not want to return any records when validation fails
// $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'session_id'     => Yii::$app->session->id
        ]);

        return $dataProvider;
    }
}
