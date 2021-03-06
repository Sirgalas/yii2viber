<?php

namespace frontend\controllers;

use common\entities\Balance;
use common\entities\BalanceLog;
use common\entities\user\User;
use common\mailers\WantDealer;
use Yii;
use common\entities\user\Client;
use common\entities\user\ClientSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController implements the CRUD actions for User model.
 */
class ClientController extends Controller
{
    const ORIGINAL_USER_SESSION_KEY = 'original_user';
    const ORIGINAL_USER_URL = 'original_user_url';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client(['dealer_id'=>Yii::$app->user->id]);
        $balance= new Balance();
        if ($model->load(Yii::$app->request->post())){
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    $balance->user_id=$model->id;
                    if ($balance->load(Yii::$app->request->post()) && $balance->save()) {
                        $transaction->commit();

                        return $this->redirect(['index']);
                    }
                }
            } catch(\Exception $e){
                $transaction->rollBack();
                return  $e->getMessage();

            }
            $transaction->rollBack();
        }

        return $this->render('create', [
            'model' => $model,
            'balance'=>$balance,
            'dealers'=>Yii::$app->user->identity->getMyDealers()
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){
        if (!Yii::$app->user->identity->amParent($id)){
            return ['output'=>'', 'message'=>'Нет доступа к этому пользователю'];
        }
        $model = $this->findModel($id);
        $balance= Balance::find()->where(['user_id'=>$id])->one();
        if (!$balance){
            $balance=new Balance(['user_id'=>$id]);
            $balance->user_id=$model->id;
        }
        if ($model->load(Yii::$app->request->post())){
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {

                    if ($balance->load(Yii::$app->request->post())){
                        if ( $balance->save()) {
                            $transaction->commit();

                            return $this->redirect(['index']);
                        }
                    }
                }
            } catch(\Exception $e){
                $transaction->rollBack();
                return  $e->getMessage();

            }
            $transaction->rollBack();
        }
        return $this->render('update', [
            'balance'=>$balance,
            'model' => $model, 'dealers'=>Yii::$app->user->identity->getMyDealers()
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->identity->isAdmin()){
            throw new ForbiddenHttpException();
        }
        $user = $this->findModel($id);
        if (BalanceLog::find()->where(['user_id'=>$id])->count()>0){
            $user->block();
        } else {
            $user->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionChangeCost($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->identity->amParent($id)){
            return ['output'=>'', 'message'=>'Нет доступа к этому пользователю'];
        }
        if (!Yii::$app->request->post('hasEditable')){
            return ['output'=>'', 'message'=>'Плохой запрос'];
        }
        $user = $this->findModel($id);
        if ($user->id == Yii::$app->user->id && !Yii::$app->user->identity->isAdmin()) {
            return ['output'=>'', 'message'=>'Вы не можете править собственный баланс'];
        }
        $db=Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $edidableIndex= $_POST['editableIndex'];
            $value=$_POST['Client'][$edidableIndex]['cost'] ;
            $user->cost = $value;
            if(!$user->save())
                throw new \Exception(json_encode($user->getErrors()));

            $transaction->commit();
            return ['output' => number_format($user->cost,2)   , 'message' => ''];
        } catch (\Exception $e){
            $transaction->rollBack();
            return ['output' => '', 'message' => 'error:: ' . $e->getMessage() ];
        }
    }

    /**
     * Изменение баланса вышестоящим дилером
     * @param $id
     * @return array
     */
    public function actionChangeBalance($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->identity->amParent($id)){
            return ['output'=>'', 'message'=>'Нет доступа к этому пользователю'];
        }
        if (!Yii::$app->request->post('hasEditable')){
            return ['output'=>'', 'message'=>'Плохой запрос'];
        }
        $user = $this->findModel($id);
        if ($user->id == Yii::$app->user->id && !Yii::$app->user->identity->isAdmin()) {
            return ['output'=>'', 'message'=>'Вы не можете править собственный баланс'];
        }
        $edidableIndex= $_POST['editableIndex'];
        $paramName = "client-$edidableIndex-balance-disp";
        $value=1 * $_POST['Client'][$edidableIndex]['balance'] ;
        if ($value<0){
            return ['output'=>'', 'message'=>'Баланс не может быть отрицательным'];
        }
        $diff = $user->balance - $value;
        if (Yii::$app->user->identity->balance + $diff <0){
            return ['output'=>'', 'message'=>'У вас недостаточно средств для этой операции'];
        }
        $db=Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {


            $user->balance = $value;

            Yii::$app->user->identity->balance +=$diff;
                if(!$user->save())
                    throw new \Exception(json_encode($user->getErrors()));

            if ($user->id !== Yii::$app->user->id ) {
                Yii::$app->user->identity->save();
            }
            $transaction->commit();
            return ['output' => number_format($user->balance) . ' SMS' , 'message' => ''];
        } catch (\Exception $e){
            $transaction->rollBack();
            return ['output' => '111', 'message' => 'error:: ' . $e->getMessage() ];
        }
    }

    /**
     * Заявка клиента "хочу стать дилером"
     * @return string
     * @throws \Exception
     */
    public function actionWantDealer(){
        if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()){
            $user=User::findOne(Yii::$app->user->identity->id);
            if(Yii::$app->user->identity->dealer_id)
                $id=Yii::$app->user->identity->dealer_id;
            else
                $id=Yii::$app->params['defaultDealer'];
            $dealer = User::findOne($id);
            $user->want_dealer=User::WANT;
            try{
                if(!$user->save())
                    throw new \Exception(json_encode($user->errors));
                if((new WantDealer())->send($user,$dealer))
                    throw new \Exception('Ошибка отправления');
                return $this->render('want-dealer');
            }catch (Exception $ex){
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash($ex->getMessage());
            }
        }
    }

    /**
     * Переключение роли пользователя
     * @param null $id
     * @return array|\yii\web\Response
     */
    public function actionSwitch($id = null){
        if(!$id && Yii::$app->session->has(self::ORIGINAL_USER_SESSION_KEY)) {
            $user = $this->findModel(Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY));
            $url = Yii::$app->session->get(self::ORIGINAL_USER_URL);

            Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
            Yii::$app->session->remove(self::ORIGINAL_USER_URL);
            Yii::$app->user->switchIdentity($user, 3600);
            return $this->redirect($url);
        }
        if ($id && !Yii::$app->session->has(self::ORIGINAL_USER_SESSION_KEY)){
            if (Yii::$app->user->identity->isClient()){
                throw new ForbiddenHttpException('У Вас нет доступа');
            }
            if (!Yii::$app->user->identity->amParent($id)){
                return ['output'=>'', 'message'=>'Нет доступа к этому пользователю'];
            }

            $user = $this->findModel($id);
            Yii::$app->session->set(self::ORIGINAL_USER_SESSION_KEY, Yii::$app->user->id);
            Yii::$app->session->set(self::ORIGINAL_USER_URL, $_SERVER['HTTP_REFERER']);
            Yii::$app->user->switchIdentity($user, 3600);
            return $this->goHome();
        }
    }

   

}
