<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.03.18
 * Time: 14:25
 */

namespace frontend\modules\api\controllers;

use PHPUnit\Framework\Exception;
use Yii;
use common\entities\ContactCollection;
use common\entities\mongo\Phone;
use frontend\entities\User;
use frontend\modules\api\components\AcViberController;

class ContactController extends AcViberController
{

    public $modelClass = 'common\entities\user\ContactCollection';

    public function actionIndex()
    {
        if (!Yii::$app->user->identity->id) {
            return 'User not Auth';
        }
        $collections = ContactCollection::find()->where(['user_id' => \Yii::$app->user->identity->id])->all();
        foreach ($collections as $collection) {
            $user = User::findOne($collection->user_id);
            $result[] = [
                'id' => $collection->id,
                'user' => $user->username,
                'title' => $collection->title,
                'type' => $collection->type,
                'created_at' => date('d-m-Y  h:i:s', $collection->created_at),
                'size' => $collection->size
            ];
        }
        if (empty($result)) {
            $result = ['error' => 'Collection not find'];
        }
        return $result;

    }

    public function actionOne()
    {
        try {
            if (!Yii::$app->user->identity->id) {
                throw new \Exception('User not Auth');
            }
            $id = Yii::$app->request->get('id');
            if (!$id) {
                throw new \Exception('id not specified');
            }
            $query = Phone::find()->where([
                'clients_id' => Yii::$app->user->identity->id,
                'contact_collection_id' => 1 * $id
            ])->all();
            if (!$query) {
                throw new \Exception('Phones not find');
            }
            foreach ($query as $phone) {
                $result[] = ['phone' => $phone->phone, 'username' => $phone->username];
            }
            if (!$result) {
                $result = ['error' => 'phone not phone'];
            }
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionCreate()
    {
        try {
            if (!Yii::$app->user->identity->id) {
                throw new \Exception('User not Auth');
            }
            $title = Yii::$app->request->post('title');
            if (!$title) {
                throw new Exception('title not specified');
            }
            $contactCollection = new ContactCollection([
                'title' => $title
            ]);
            if (!$contactCollection->save()) {
                throw new Exception (var_dump($contactCollection->getErrors()));
            }
            return [
                'success' => 'collection create',
                'id_colection'=>$contactCollection->id
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionUpdate()
    {
        try {
            if (!Yii::$app->user->identity->id) {
                throw new \Exception('User not Auth');
            }
            $id = Yii::$app->request->post('id');
            if (!$id) {
                throw new \Exception('id not specified');
            }
            $title = Yii::$app->request->post('title');
            if (!$title) {
                throw new Exception('title not specified');
            }
            $contactCollection = ContactCollection::findOne(['id' => $id]);
            if (!$contactCollection) {
                throw new Exception('contact collection not find');
            }
            $contactCollection->title = $title;
            if ($contactCollection->save()) {
                throw new Exception (var_dump($contactCollection->errors));
            }
            return ['success' => 'collection create'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionDelete()
    {
        try {
            if (!Yii::$app->user->identity->id) {
                throw new \Exception('User not Auth');
            }
            $id = Yii::$app->request->post('id');
            if (!$id) {
                throw new \Exception('id not specified');
            }

            $contactCollection = ContactCollection::findOne(['id' => $id]);
            if (!$contactCollection) {
                throw new Exception('contact collection not find');
            }
            if ($contactCollection->delete()) {
                throw new Exception (var_dump($contactCollection->getFirstError()));
            }
            return ['success' => 'collection create'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionCreatePhones()
    {
        $phone = new Phone();
        if (!Yii::$app->user->identity->id) {
            return ['error' => 'User not Auth'];
        }
        $id = Yii::$app->request->post('id');
        if (!$id) {
            throw new \Exception('id not specified');
        }
        if (Yii::$app->request->post('url')) {
            $file = file_get_contents(Yii::$app->request->post('url'));
            if (!$file) {
                return ['error' => 'file not find'];
            }
            $txt = '';
            foreach (json_decode($file) as $oneFile) {
                $txt .= $oneFile->phone . '%' . $oneFile->user . "\n";
            }

        } elseif (Yii::$app->request->post('text')) {
            $txt = '';
            foreach (json_decode(Yii::$app->request->post('text')) as $oneFile) {
                $txt .= $oneFile->phone . '%' . $oneFile->user . '\n';
            }
        } else {
            $result = ['error' => 'url, phone not specified'];
            return $result;
        }
        try {
            $phone->importText($id, $txt, Yii::$app->user->identity->id);
            return ['success' => 'phones save'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionUpdatePhones()
    {
        if (!Yii::$app->user->identity->id) {
            return ['error' => 'User not Auth'];
        }
        if (!Yii::$app->request->post('id')) {
            return ['error' => 'id not specified'];
        }
        $id=(int)Yii::$app->request->post('id');
        if (!Yii::$app->request->post('phone')) {
            return ['error' => 'phone not specified'];
        }
        $phone = Phone::findOne(['contact_collection_id'=>$id,'phone' => (int)Yii::$app->request->post('phone')]);
        if (!$phone) {
            return ['error' => 'phone not find'];
        }
        if (Yii::$app->request->post('phone-update')) {
            $phone->phone = (int)Yii::$app->request->post('phone-update');
        }
        if (Yii::$app->request->post('username-update')) {
            $phone->username = Yii::$app->request->post('username-update');
        }
        if (!$phone->save()) {
            return $phone->getFirstErrors();
        }
        return ['success' => 'phone update'];
    }

    public function actionDeletePhones()
    {
        $phone = new Phone();
        if (!Yii::$app->user->identity->id) {
            return ['error' => 'User not Auth'];
        }
        $id = Yii::$app->request->post('id');
        if (!$id) {
            throw new \Exception('id not specified');
        }
        if (Yii::$app->request->post('url')) {
            $file = file_get_contents(Yii::$app->request->post('url'));
            if (!$file) {
                return ['error' => 'file not find'];
            }
            $txt = '';
            foreach (json_decode($file) as $onePhone) {
                $arrPhone[]=(int)$onePhone->phone;
            }

        } elseif (Yii::$app->request->post('text')) {
            $txt = '';
            foreach (json_decode(Yii::$app->request->post('text')) as $onePhone) {
                $arrPhone[]=$onePhone->phone;
            }
        } else {
            return ['error' => 'phone not specified'];
        }
        try {
            if(!$phone->deleteAll(['phone'=>$arrPhone]))
                throw new \Exception('error deleting');
            return ['success' => 'phones delete'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}