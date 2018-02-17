<?php

namespace frontend\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\ViberTransaction;

/**
 * PhoneSearch represents the model behind the search form of `common\entities\Phone`.
 */
class ReportSearch extends ViberTransaction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id',  'created_at'], 'integer'],
            [['status',], 'safe'],
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
        $query = ViberTransaction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
        'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id'=>Yii::$app->user->identity->id,
            'created_at'=>$this->created_at,
            'status'=>$this->status
            ]);

        return $dataProvider;
    }
}