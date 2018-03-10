<?php

namespace common\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\Scenario;

/**
 * ScenarioSearch represents the model behind the search form of `common\entities\Scenario`.
 */
class ScenarioSearch extends Scenario
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at'], 'integer'],
            [['provider', 'name', 'from1', 'channel1', 'from2', 'channel2', 'from3', 'channel3', 'provider_scenario_id'], 'safe'],
            [['default'], 'boolean'],
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
        $query = Scenario::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
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
            'default' => $this->default,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'provider', $this->provider])
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'from1', $this->from1])
            ->andFilterWhere(['ilike', 'channel1', $this->channel1])
            ->andFilterWhere(['ilike', 'from2', $this->from2])
            ->andFilterWhere(['ilike', 'channel2', $this->channel2])
            ->andFilterWhere(['ilike', 'from3', $this->from3])
            ->andFilterWhere(['ilike', 'channel3', $this->channel3])
            ->andFilterWhere(['ilike', 'provider_scenario_id', $this->provider_scenario_id]);

        return $dataProvider;
    }
}
