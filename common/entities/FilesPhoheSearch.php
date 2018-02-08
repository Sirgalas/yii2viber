<?php

namespace common\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\FilesPhohe;

/**
 * FilesPhoheSearch represents the model behind the search form of `common\entities\FilesPhohe`.
 */
class FilesPhoheSearch extends FilesPhohe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'month', 'years'], 'integer'],
            [['file'], 'safe'],
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
        $query = FilesPhohe::find();

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
            'month' => $this->month,
            'years' => $this->years,
        ]);

        $query->andFilterWhere(['ilike', 'file', $this->file]);

        return $dataProvider;
    }
}
