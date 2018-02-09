<?php

namespace common\entities\user;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\user\Client;

/**
 * UserSearch represents the model behind the search form of `common\entities\user\User`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'confirmed_at', 'blocked_at', 'created_at', 'updated_at', 'flags', 'last_login_at', 'dealer_id'], 'integer'],
            [['username', 'email', 'password_hash', 'auth_key', 'unconfirmed_email', 'registration_ip', 'type', 'image'], 'safe'],
            [['balance'], 'number'],
            [['dealer_confirmed'], 'boolean'],
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
        $query = Client::find();

        // add conditions that should always apply here
        $ids = Yii::$app->user->identity->getClildList();
        if ($ids !== -1) {
            $query->andWhere(['in', 'id', $ids]);
        }
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
            'confirmed_at' => $this->confirmed_at,
            'blocked_at' => $this->blocked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flags' => $this->flags,
            'last_login_at' => $this->last_login_at,
            'dealer_id' => $this->dealer_id,
            'balance' => $this->balance,
            'dealer_confirmed' => $this->dealer_confirmed,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'password_hash', $this->password_hash])
            ->andFilterWhere(['ilike', 'auth_key', $this->auth_key])
            ->andFilterWhere(['ilike', 'unconfirmed_email', $this->unconfirmed_email])
            ->andFilterWhere(['ilike', 'registration_ip', $this->registration_ip])
            ->andFilterWhere(['ilike', 'type', $this->type])
            ->andFilterWhere(['ilike', 'image', $this->image]);


        return $dataProvider;
    }
}
