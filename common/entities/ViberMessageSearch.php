<?php

namespace common\entities;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\entities\ViberMessage;

/**
 * ViberMessageSearch represents the model behind the search form about `common\entities\ViberMessage`.
 */
class ViberMessageSearch extends ViberMessage
{
    public $username;

    public function rules()
    {
        return [
            [['id', 'user_id', 'date_start', 'date_finish', 'limit_messages'], 'integer'],
            [['title', 'text', 'image', 'title_button', 'url_button', 'type', 'alpha_name', 'time_start', 'time_finish', 'status','username'], 'safe'],
            [['cost', 'balance'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {

        $query = ViberMessage::find()->where(['user_id' => Yii::$app->user->identity->id,]);
        $query->joinWith(['user']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $dataProvider->sort->attributes['username'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];
        $query->andFilterWhere([
            'id' => $this->id,

            'date_start' => $this->date_start,
            'date_finish' => $this->date_finish,
            'limit_messages' => $this->limit_messages,
            'cost' => $this->cost,
            'balance' => $this->balance,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'title_button', $this->title_button])
            ->andFilterWhere(['like', 'url_button', $this->url_button])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'alpha_name', $this->alpha_name])
            ->andFilterWhere(['like', 'time_start', $this->time_start])
            ->andFilterWhere(['like', 'time_finish', $this->time_finish])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'user.username', $this->username]);
        return $dataProvider;
    }
}
