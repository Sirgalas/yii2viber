<?php

namespace backend\modules\homepage\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\Config;

/**
 * ServicesSearch represents the model behind the search form of `common\entities\Config`.
 */
class ServicesSearch extends Config
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['param', 'text', 'description'], 'safe'],
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
        $query = Config::find();

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
            'description'=>'services'
        ]);

        $query->andFilterWhere(['ilike', 'param', $this->param])
            ->andFilterWhere(['ilike', 'text', $this->text]);

        return $dataProvider;
    }
}
