<?php

namespace frontend\controllers;

use Aws\Common\Exception\RuntimeException;
use common\entities\mongo\Phone;
use common\entities\mongo\PhoneSearch;
use frontend\forms\FileForm;
use Yii;
use common\entities\ContactCollection;
use common\entities\ContactCollectionSearch;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\entities\Phone as PgPhone;
use common\entities\PhoneSearch as PgPhoneSearch;
use yii\web\UploadedFile;
use frontend\forms\ContactCollectionModalForm;

/**
 * ContactCollectionController implements the CRUD actions for ContactCollection model.
 */
class ContactCollectionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ContactCollection models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactCollectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * Displays a single ContactCollection model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $searchModel = new PhoneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'modelCollections' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Creates a new ContactCollection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContactCollection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $cnt = Phone::find()->where(['contact_collection_id' => $this->id])->count();
            $model->size = $cnt;
            $model->save();
            return $this->redirect(['index']);
        }
        $phoneSearchModel = new PhoneSearch();
        $phoneDataProvider = $phoneSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', compact('model', 'phoneSearchModel', 'phoneDataProvider'));
    }

    /**
     * Updates an existing ContactCollection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        try {
            if (! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->identity->id != $model->user_id) { //TODO Корректировать условие!!!
                throw new NotFoundHttpException('Этот пользователь вам не пренадлижит', 403);
            }
            if (Yii::$app->request->post('hasEditable')) {
                try {
                    $phone = $model->phoneSave(Yii::$app->request, $model->id);
                    return $phone;
                } catch (RuntimeException $ex) {
                    Yii::$app->errorHandler->logException($ex);
                    Yii::$app->session->setFlash('error', $ex->getMessage());
                }
            }
            if ($model->load(Yii::$app->request->post())) {
                $cnt = Phone::find()->where(['contact_collection_id' => (int) $id])->count();
                if ($model->size != $cnt) {
                    $model->size = $cnt;
                }
                if ($model->save()) {
                    return $this->redirect(['index']);
                }
            }
            $phoneSearchModel = new PhoneSearch();
            $phoneSearchModel->contact_collection_id = $id;
            $modalForm = new FileForm();
            $contactCollection = ArrayHelper::map(ContactCollection::find()->asArray()->all(), 'id', 'title');
            $contactForm = new ContactCollectionModalForm();
            $phoneDataProvider = $phoneSearchModel->search(Yii::$app->request->queryParams);

            return $this->render('update',
                compact('model', 'phoneSearchModel', 'phoneDataProvider', 'modalForm', 'contactCollection',
                    'contactForm'));
        } catch (NotFoundHttpException $ex) {
            return $this->render('/site/error', ['name' => $model->title, 'message' => $ex->getMessage()]);
        }
    }

    /**
     * Deletes an existing ContactCollection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContactCollection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ContactCollection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactCollection::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionNewPhones($id)
    {
        $collection = ContactCollection::findOne($id);
        if (! $collection) {
            return 'Ошибка в запросе. Обновите страницу';
        }

        // TODO проверить права на коллекцию
        $phone = new Phone();
        $result=$phone->importText($id, $_POST['txt']);
        $cnt = Phone::find()->where(['contact_collection_id' => (int) $id])->count();
        if ($collection->size != $cnt) {
            $collection->size = $cnt;
        }
        if(!$collection->save())
            return var_dump($collection->getErrors());
        return $result;
    }

    public function actionRemovePhones($id)
    {
        $collection = ContactCollection::findOne($id);
        if (! $collection) {
            return 'Ошибка в запросе. Обновите страницу';
        }
        // TODO проверить права на коллекцию
        $phone = new Phone();
        $result=$phone->removeList($_POST['ids']);
        $cnt = Phone::find()->where(['contact_collection_id' => (int) $id])->count();
        if ($collection->size != $cnt) {
            $collection->size = $cnt;
        }
        $collection->save();
        return $result;
    }

    public function actionImportFile()
    {
        $form = new FileForm();
        $phone = new Phone();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('FileForm');
            try {
                $resource = UploadedFile::getInstance($form, 'file');
                $result = $phone->pointer($resource, Yii::$app->request->post(), $post, $form);

                if (! $result) {
                    throw new \Exception('Ошибка импорта');
                }
                $collection = ContactCollection::findOne($result);
                $cnt = Phone::find()->where(['contact_collection_id' => (int) $result])->count();
                if ($collection->size != $cnt) {
                    $collection->size = $cnt;
                }
                $collection->save();
                return $this->redirect(['/contact-collection/update', 'id' => $result]);
            } catch (\Exception $ex) {
                Yii::$app->session->setFlash($ex->getMessage());

                return $this->redirect(['/contact-collection/update', 'id' => $post['collection_id']]);
            }
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionImportCollection()
    {
        $form = new ContactCollectionModalForm();
        $phone = new Phone();
        if ($form->load(Yii::$app->request->post())) {
            try {
                $result = $phone->importCollection(Yii::$app->request->post('ContactCollectionModalForm'));
                $collection = ContactCollection::findOne($result);
                $cnt = Phone::find()->where(['contact_collection_id' => (int) $result])->count();
                if ($collection->size != $cnt) {
                    $collection->size = $cnt;
                }
                $collection->save();
                return $this->redirect(['/contact-collection/update', 'id' => $result]);
            } catch (\Exception $ex) {
                Yii::$app->session->setFlash($ex->getMessage());

                return $this->redirect([
                    '/contact-collection/update',
                    'id' => Yii::$app->request->post('ContactCollectionModalForm')['collection_id'],
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionExport($id)
    {
        $this->layout = '';
        $blockSize =1000;
        $collection = ContactCollection::findOne($id);
        if (! Yii::$app->user->identity->amParent($collection->user_id) && Yii::$app->user->id != $collection->user_id) {
            throw new NotFoundHttpException('Этот пользователь вам не принадлежит', 403);
        }

        $now = gmdate("D, d M Y H:i:s");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: " . $now . " GMT");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export_'.date('Ymd_H').'_Phone_list.csv"');
        $position = 0;
        $df = fopen("php://output", 'w');
        while (true) {

            $phones = Phone::find()->where(['contact_collection_id' =>(int) $id])->asArray()->limit($blockSize)->offset($position)->all();
            if (count($phones) == 0) {
                break;
            }
            foreach ($phones as $phone) {

                fputcsv($df, [$phone['phone']]);
            }

            $position += $blockSize;
        }
        fclose($df);
        exit;
    }
}
