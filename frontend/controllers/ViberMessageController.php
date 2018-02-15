<?php

namespace frontend\controllers;

use common\entities\ContactCollection;
use common\entities\MessageContactCollection;
use common\entities\mongo\Phone;
use frontend\entities\User;
use Yii;
use common\entities\ViberMessage;
use common\entities\ViberMessageSearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ViberMessageController implements the CRUD actions for ViberMessage model.
 */
class ViberMessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ViberMessage models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ViberMessageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ViberMessage model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }



    /**
     * Updates an existing ViberMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = 0)
    {
        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model=new ViberMessage();
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->send()) {
                return $this->redirect(['index']);
            }
        }

        $contact_collections = ContactCollection::find()->andWhere(['user_id' => $model->user_id])->select([
            'id',
            'title',
        ])->orderBy('title')->asArray()->all();
        $contact_collections = ArrayHelper::map($contact_collections, 'id', 'title');

        $model->assign_collections = MessageContactCollection::find()->select(['contact_collection_id'])->andWhere(['viber_message_id' => $id])->column();

        $clients = ArrayHelper::map(User::find()->where(['dealer_id' => Yii::$app->user->identity->id])->all(), 'id',
            'username');
        $clients[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
        return $this->render('viberForm', compact('model', 'contact_collections', 'clients'));
    }

    /**
     * Deletes an existing ViberMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->findModel($id)->delete();
            $transaction->commit();
        } catch (\Exception $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash(
                'error',
                'Ошибка удаления.'

            );
        }
        return $this->redirect(['index']);
    }

    public function actionCost()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post('data');
        $entities = ViberMessage::findOne(Yii::$app->request->post('id'));
        try {
            $cost = $entities->Cost($data);
            $balance = $entities->userBalanse($cost);

            return ['cost' => $cost, 'balance' => $balance, 'result' => 'ok'];
        } catch (\Exception $ex) {
            Yii::$app->errorHandler->logException($ex);

            return ['result' => 'error', 'message' => $ex->getMessage()];
        }
    }

    /**
     * Finds the ViberMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ViberMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ViberMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAssignCollection($id)
    {
        $model = ViberMessage::findOne($id);
        if (! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
            throw new NotFoundHttpException('Этот пользователь вам не принадлежит', 403);
        }
        try {
            MessageContactCollection::assign($id, $model->user_id, $_POST['data']);
            $model->save();

            return 'ok';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
