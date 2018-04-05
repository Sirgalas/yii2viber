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
class StatisticsMongoSearch extends Message_Phone_List
{

    public $create_at;
    /**
     * @inheritdoc
     */
    /*public function rules()
    {
        return [
            [['date_viewed','date_delivered'], 'integer'],
            [['status','phone','message_id',], 'safe'],
        ];
    }*/

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
        $query = Message_Phone_List::find();
        $id_messageFromCollection = [];
        if ($params['contactCollection'] != "") {
            $collections = MessageContactCollection::find()
                ->where(['contact_collection_id' => $params['contactCollection']])
                ->select('viber_message_id')
                ->all();
            foreach ($collections as $viber_message_id) {
                $id_messageFromCollection[] = $viber_message_id->viber_message_id;
            }
        }

        if (isset($params['dateTo'])) {
            $dateTo = strtotime($params['dateTo'] . ' 23:59:59');
        } else {
            $dateTo = time();
        }

        if (isset($params['dateFrom'])) {
            $dateFrom = strtotime($params['dateFrom'] . ' 00:00:01');
        } else {
            $dateFrom = strtotime('01.01.2018');
        }
        $idsTransaction[] = 0;
        $transactionsIdFromUser = ViberTransaction::find()
            ->where([
                'user_id' => Yii::$app->user->identity->id
            ])
            ->andFilterWhere([
                '>=',
                'created_at',
                $dateFrom
            ])
            ->andFilterWhere([
                '<=',
                'created_at',
                $dateTo])
            ->all();

        if ($transactionsIdFromUser) {
            foreach ($transactionsIdFromUser as $transactionIdFromUser) {
                $idsTransaction[] = $transactionIdFromUser->id;
            }
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($idsTransaction)) {
            $query->andFilterWhere(['in', 'transaction_id', $idsTransaction]);
        } else {
            $query->where(['transaction_id' => 0]);
        }
        if (isset($params['titleSearch'])) {
            $query->andFilterWhere(['in', 'phone', $params['titleSearch']]);
        }
        if (isset($transactionIds)) {
            $query->andFilterWhere(['in', 'transaction_id', $transactionIds]);
        }
        if (isset($params['status'])) {
            $query->andFilterWhere(['in', 'status', $params['status']]);
        }
        if (!empty($id_messageFromCollection)) {
            $query->andFilterWhere(['in', 'message_id', $id_messageFromCollection]);
        }
        return $dataProvider;
    }
}

