<?php

namespace common\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\Balance;

/**
 * BalanceSearch represents the model behind the search form of `common\entities\Balance`.
 */
class BalanceSearch extends Balance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'viber', 'telegram', 'wechat'], 'integer'],
            [['viber_price', 'whatsapp', 'whatsapp_price', 'telegram_price', 'wechat_price'], 'safe'],
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
        $query = Balance::find();

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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'viber' => $this->viber,
            'telegram' => $this->telegram,
            'wechat' => $this->wechat,
        ]);

        $query->andFilterWhere(['ilike', 'viber_price', $this->viber_price])
            ->andFilterWhere(['ilike', 'whatsapp', $this->whatsapp])
            ->andFilterWhere(['ilike', 'whatsapp_price', $this->whatsapp_price])
            ->andFilterWhere(['ilike', 'telegram_price', $this->telegram_price])
            ->andFilterWhere(['ilike', 'wechat_price', $this->wechat_price]);

        return $dataProvider;
    }
}
