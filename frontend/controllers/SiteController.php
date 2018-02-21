<?php
namespace frontend\controllers;

use frontend\forms\ViberTestForm;
use JsonSchema\Exception\RuntimeException;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\forms\LoginForm;

use frontend\forms\ContactForm;
//use frontend\services\auth\SignapService;
/**
 * Site controller
 */
class SiteController extends Controller
{



    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest){
            return $this->render('index');
        };
        $model = new ViberTestForm();
        if ($model->load(Yii::$app->request->post())){
           if ($model->validate()){
             if ($model->send()){
                 return $this->render('viberSuccess' ,compact('model'));
             }
           }

        }
        return $this->render('viberTestForm', compact('model'));
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);

    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $form = new ContactForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try{
                $this->contactService->send($form);
                Yii::$app->session->setFlash('success',Yii::t('app','Thank You'));
                return $this->goHome();
            }catch (RuntimeException $ex){
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error',Yii::t('app','Sending Error'));
            }
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $form,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionAdvertising(){
        return $this->render('advertising');
    }
}
