<?php

namespace common\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\BalanceLog;

/**
 * BalanceLogSearch represents the model behind the search form of `common\entities\BalanceLog`.
 */
class BalanceLogSearch extends BalanceLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [[ 'controller_id', 'action_id', 'type', 'fixed', 'query', 'post', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */

    public function scenarios()
    {
        $scenarios =  Model::scenarios();
        $scenarios['search'] = $scenarios['default'];//Scenario Values Only Accepted

        return $scenarios;

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
        $query = BalanceLog::find();
        $this->scenario ='search';
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
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
        ]);

        $query
            //->andFilterWhere(['ilike', 'old_balance', $this->old_balance])
            //->andFilterWhere(['ilike', 'new_balance', $this->new_balance])
            //->andFilterWhere(['ilike', 'diff_balance', $this->diff_balance])
            ->andFilterWhere(['ilike', 'controller_id', $this->controller_id])
            ->andFilterWhere(['ilike', 'action_id', $this->action_id])
            ->andFilterWhere(['ilike', 'type', $this->type])
            ->andFilterWhere(['ilike', 'fixed', $this->fixed])
            ->andFilterWhere(['ilike', 'query', $this->query])
            ->andFilterWhere(['ilike', 'post', $this->post]);

        return $dataProvider;
    }
}
