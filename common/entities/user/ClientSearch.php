<?php

namespace common\entities\user;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\Balance;

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
            [['id', 'confirmed_at',  'updated_at', 'flags', 'last_login_at', 'dealer_id'], 'integer'],
            [['username', 'email', 'password_hash', 'auth_key', 'unconfirmed_email', 'registration_ip', 'type', 'image', 'blocked_at', 'created_at'], 'safe'],
            //[['balance'], 'number'],
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
        $query = Client::find()->where('coalesce(blocked_at, 0)<1' );
        $query->joinWith(['balance']);
        // add conditions that should always apply here
        $ids = Yii::$app->user->identity->getChildList();
        if ($ids !== -1) {
            $query->andWhere(['in', User::tableName().'.id', $ids]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['username'=>SORT_ASC]]
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
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            'dealer_id' =>  $this->dealer_id,
            'flags' => $this->flags,
            'last_login_at' => $this->last_login_at,

            //'balance' => $this->balance,

        ]);
        if ( $this->created_at){
            $query->andFilterWhere(['like', "cast(abstime(created_at) as varchar) ", $this->created_at]);
        }

        if ($this->dealer_confirmed){
            if ($this->dealer_confirmed==='t'){
                $query->andFilterWhere(['dealer_confirmed','f']);
            } else {
                $query->andFilterWhere(['!=','dealer_confirmed','f']);
            }
        }
        if (   $this->blocked_at === '0'){
            $query->andFilterWhere(['<','blocked_at',10]);
        } else if (  $this->blocked_at === '1'){
            $query->andFilterWhere(['>','blocked_at',10]);
        }

        if (   $this->confirmed_at === '0'){
            $query->andFilterWhere(['<','confirmed_at',10]);
        } else if (  $this->confirmed_at === '1'){
            $query->andFilterWhere(['>','confirmed_at',10]);
        }

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
