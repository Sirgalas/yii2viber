<?php

namespace frontend\controllers;

use common\entities\ContactCollection;
use common\entities\MessageContactCollection;

use frontend\entities\User;

use Yii;
use common\entities\ViberMessage;
use common\entities\ViberMessageSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\services\message\ViberMessageServices;

/**
 * ViberMessageController implements the CRUD actions for ViberMessage model.
 */
class ViberMessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'moderate' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ViberMessage models.
     *
     * @return mixed
     * @throws \yii\base\InvalidArgumentException
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
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Updates an existing ViberMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id = 0)
    {
        if ($id) {
            $model = $this->findModel($id);
            if (! Yii::$app->user->identity->isAdmin() && ! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
                throw new NotFoundHttpException('Эта рассылка вам не принадлежит', 403);
            }
        } else {
            $model = new ViberMessage();
        }
        if (! $model->status) {
                $model->status = ViberMessage::STATUS_PRE;
        }
        if ($model->load(Yii::$app->request->post())) {
            $services= new ViberMessageServices();
            if($services->send(Yii::$app->request->post(),$model)) {
                return $this->redirect(['index']);
            }
        }
        $model->setAttribute('status', $model->getOldAttribute('status'));
        $contact_collections = ContactCollection::find()->andWhere(['user_id' => $model->user_id ?: Yii::$app->user->identity->id])->select([
            'id',
            'title',
        ])->orderBy('title')->asArray()->all();
        $contact_collections = ArrayHelper::map($contact_collections, 'id', 'title');
        $model->assign_collections = MessageContactCollection::find()->select(['contact_collection_id'])->andWhere(['viber_message_id' => $id])->column();
        $clients = ArrayHelper::map(User::find()->where(['dealer_id' => Yii::$app->user->identity->id])->all(), 'id',
            'username');
        $clients[Yii::$app->user->identity->id] = Yii::$app->user->identity->username;
        return $this->render('viberForm', compact('model', 'contact_collections'));
    }

    /**
     * Deletes an existing ViberMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = $this->findModel($id);
            if (! Yii::$app->user->identity->isAdmin() && ! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
                throw new NotFoundHttpException('Этот рассылка вам не принадлежит', 403);
            }
            if (!$model->isDeleteble()) {
                throw new NotFoundHttpException('Удаление этой рассылка невозможно', 403);
            }
            $model->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ошибка удаления. ' . $e->getMessage());


        }

        return $this->redirect(['index']);
    }

    /***
     * Расчет стоимости рассылки (ajax)
     * @return array
     */
    public function actionCost()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->post('data');
        try {
            $cost = ViberMessage::Cost($data);
            if (Yii::$app->request->post('id')) {
                $entities = ViberMessage::findOne(Yii::$app->request->post('id'));
                if ($entities->channel===Yii::$app->request->post('channel')) {
                    $balance = $entities->userBalanse($cost, Yii::$app->request->post('channel'));
                } else {
                    $balance = ViberMessage::calcRestBalance( $cost,  Yii::$app->request->post('channel'));
                }
            } else {
                $balance = ViberMessage::calcRestBalance( $cost,  Yii::$app->request->post('channel'));
            }

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
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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

    public function actionModerate()
    {

        $data = Yii::$app->request->post();

        if (Yii::$app->user->identity->isAdmin()) {

            $vm = $this->findModel($data['ViberMessage']['id']);
            if (isset($_POST['allow'])) {
                $vm->status = ViberMessage::STATUS_NEW;
                $vm->save();
            }
            if (isset($_POST['disallow'])) {
                $vm->status = ViberMessage::STATUS_FIX;
                $vm->save();
            }
            if (isset($_POST['close'])) {
                $vm->status = ViberMessage::STATUS_CLOSED;
                $vm->save();
            }

            return $this->redirect('/viber-message');
        }
        throw new ForbiddenHttpException();
    }
}
