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

        if(isset($params['titleSearch'])){
                $titleId=Message_Phone_List::find()->where(['phone'=>$params['titleSearch']])->one();

        }

        if($params['user_id']!=""){
            $user=User::find()->select('id')->where(['id_dealer'=>Yii::$app->user->identity->id,'id'=>$params['user_id']])->one();
            if($user){
                $allTransaction=ViberTransaction::find()->where(['user_id'=>$params['user_id']])->all();
                foreach ($allTransaction as $oneTransaction){
                    $transactionIds[]=$oneTransaction;
                }
            }
        }
        $id_messageFromCollection=[];
        if($params['contactCollection']!=""){
            $collections=MessageContactCollection::find()
            ->where(['contact_collection_id'=>$params['contactCollection']])
            ->select('viber_message_id')
            ->all();
            foreach ($collections as $viber_message_id){
                $id_messageFromCollection[]=$viber_message_id->viber_message_id;
            }
        }
        if(!empty($params['status'])){
            $ids=Message_Phone_List::find()->where(['status'=>$params])->select(['_id'])->column();
            foreach ($ids as $id){
                $messagePhoneListId[]=(string)$id;
            }
        }
        if(isset($params['dateTo'])){
            $dateTo=strtotime($params['dateTo']. ' 23:59:59');
        }else{
            $dateTo=time();
        }
        if($params['dateFrom']!='') {
            $transactionAll=ViberTransaction::find()->andFilterWhere(['>=', 'created_at', strtotime($params['dateFrom'])])->andFilterWhere(['<=', 'created_at', $dateTo]);
            foreach ($transactionAll as $transactionOne){
                $transactionIdsFromDate[]=$transactionOne;
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

        if(isset($transactionIds))
            $query->andFilterWhere(['in','transaction_id', $transactionIds]);
        if(!empty($messagePhoneListId))
            $query->andFilterWhere(['in','_id',$messagePhoneListId]);
        if(!empty($id_messageFromCollection))
            $query->andFilterWhere(['in','message_id',$id_messageFromCollection]);

        return $dataProvider;
    }
}

