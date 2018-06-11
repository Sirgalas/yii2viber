<?php
/**
* @property int $message_id
 */

namespace frontend\search;


use common\entities\ViberTransaction;
use common\entities\ContactCollection;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ReportSearch extends ViberTransaction
{

    public $collection_id;
    public $message_id;

    public function rules()
    {
        return [
            [['viber_message_id', 'status', 'created_at', 'collection_id','message_id'], 'safe']
        ];
    }

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

        if (isset($params['collection_id'])) {
            $collection_ids = ContactCollection::find()->where(['id' => $params['collection_id']])->all();
            foreach ($collection_ids as $collection_id) {
                $messageId[] = $collection_id->viberMessage->id;
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);

        $dataProvider->pagination=false;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => \Yii::$app->user->identity->id,
            'viber_message_id' => $this->viber_message_id,
            'created_at' => $this->created_at,
            'status' => $this->status
        ]);
        if (!empty($messageId)) {
            $query->andFilterWhere(['in', 'viber_message_id', $messageId]);
        }
        return $dataProvider;
    }

    public function searchApi($params)
    {
        $query = ViberTransaction::find();

        if (isset($params['collection_id'])) {
            $collection_ids = ContactCollection::find()->where(['id' => $params['collection_id']])->all();
            foreach ($collection_ids as $collection_id) {
                $messageId[] = $collection_id->viberMessage->id;
            }
        }
        $query->andFilterWhere([
            'user_id' => \Yii::$app->user->identity->id,
            'viber_message_id' => $params['message_id'],
            'created_at' => $this->created_at,
            'status' => $this->status
        ]);
        if (!empty($messageId)) {
            $query->andFilterWhere(['in', 'viber_message_id', $messageId]);
        }
        if (!$this->validate())
            throw new NotFoundHttpException('request not valid');

        return $query->all();
    }
    public function searchApiOne($params)
    {
        $query = ViberTransaction::find();

        if (isset($params['collection_id'])) {
            $collection_ids = ContactCollection::find()->where(['id' => $params['collection_id']])->all();
            foreach ($collection_ids as $collection_id) {
                $messageId[] = $collection_id->viberMessage->id;
            }
        }
        $query->andFilterWhere([
            'user_id' => \Yii::$app->user->identity->id,
            'viber_message_id' => $params['message_id'],
            'created_at' => $this->created_at,
            'status' => $this->status
        ]);
        if (!empty($messageId)) {
            $query->andFilterWhere(['in', 'viber_message_id', $messageId]);
        }
        if (!$this->validate())
            throw new NotFoundHttpException('request not valid');

        $messagesId=$query->one();

        $messagesId->phones=$messagesId->StatusPhone();
        return $messagesId;
    }
}