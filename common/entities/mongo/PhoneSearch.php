<?php

namespace common\entities\mongo;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\mongo\Phone;

/**
 * ContactCollectionSearch represents the model behind the search form of `common\entities\ContactCollection`.
 */
class PhoneSearch extends Phone
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'integer'],
            [['username'], 'safe'],

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
    public function search($params,$id)
    {
        $query = Phone::find()->where(['contact_collection_id'=>$id]);

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

        $query->andFilterWhere(['like', 'phone', $this->title])
            ->andFilterWhere(['like', 'username', $this->type]);

        return $dataProvider;
    }


}
