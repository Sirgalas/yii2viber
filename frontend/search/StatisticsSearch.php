<?php

namespace frontend\search;

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
class StatisticsSearch extends ViberTransaction
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id',  'created_at'], 'integer'],
            [['status','titleSearch','contactCollection','status','dateFrom','dateTo','viberMessage'], 'safe'],
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
        $query = ViberTransaction::find();

        if(isset($params['titleSearch'])){
            if(is_int($params['titleSearch'])){
                $titleId=ViberTransaction::find()->andFilterWhere(['?|','phones',$params['titleSearch']])->one();
            }
            else{
                $idMessageViber=ViberMessage::find()->andFilterWhere(['like','title',$params['titleSearch']])->select(['id'])->column();
            }
        }

        if($params['user_id']!=""){
            $user=User::find()->select('id')->where(['id_dealer'=>Yii::$app->user->identity->id,'id'=>$params['user_id']])->one();
            if($user)
                $user_id=$user->id;
            else
                $user_id=Yii::$app->user->identity->id;
        }else{
            $user_id=Yii::$app->user->identity->id;
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
            'user_id'=>$user_id,
            'created_at'=>$this->created_at,
            'status'=>$this->status
            ]);
        if(isset($titleId))
            $query->andFilterWhere(['=','id', $this->username]);
        if(!empty($idMessageViber))
            $query->andFilterWhere(['in','viber_message_id',$idMessageViber]);
        if(!empty($id_messageFromCollection))
            $query->andFilterWhere(['in','viber_message_id',$id_messageFromCollection]);

        return $dataProvider;
    }
}

/*$collections=ContactCollection::find()->where(['title'=>$params['titleSearch']])->one();
                foreach ($collections as $collection){
                    $id_message=$collection->viberMessage->id;
                }*/