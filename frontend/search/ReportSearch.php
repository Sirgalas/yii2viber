<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.18
 * Time: 21:20
 */

namespace frontend\search;


use common\entities\ViberTransaction;
use common\entities\ContactCollection;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ReportSearch extends ViberTransaction
{

    public $collection_id;
    public function rules()
    {
        return [
            [['viber_message_id', 'status', 'created_at','collection_id'], 'safe']];
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

        if(isset($params['collection_id'])){
            $collection_ids=ContactCollection::find()->where(['id'=>$params['collection_id']])->all();
            foreach ($collection_ids as $collection_id){
                $messageId[]=$collection_id->viberMessage->id;
            }
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
            'user_id'=>\Yii::$app->user->identity->id,
            'viber_message_id'=>$this->viber_message_id,
            'created_at'=>$this->created_at,
            'status'=>$this->status
        ]);
        if(!empty($messageId))
        $query->andFilterWhere(['in','viber_message_id',$messageId]);
        return $dataProvider;
    }
}