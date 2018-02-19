<?php

namespace frontend\search;

use common\entities\mongo\Message_Phone_List;
use common\entities\user\User;
use common\entities\MessageContactCollection;
use common\entities\ViberMessage;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\ViberTransaction;

/**
 * PhoneSearch represents the model behind the search form of `common\entities\Phone`.
 */
class ReportMongoSearch extends Message_Phone_List
{
    

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Message_Phone_List::find();
        $query->andFilterWhere(['=','transaction_id',(int)$params['id']]);
        $dataProvider = new ActiveDataProvider([
        'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;

    }
}

