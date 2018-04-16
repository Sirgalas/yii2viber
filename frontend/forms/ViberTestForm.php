<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 15.02.2018
 * Time: 12:48
 */

namespace frontend\forms;

use common\components\Viber;
use common\entities\ContactCollection;
use common\entities\MessageContactCollection;
use common\entities\mongo\Phone;
use common\entities\ViberMessage;
use yii\db\Exception;
use yii\web\UploadedFile;
use Yii;
use yii\base\Model;

class ViberTestForm extends Model
{
    public $title;

    public $text;

    public $image;

    public $title_button;

    public $url_button;

    public $type;

    public $alpha_name;

    public $time_start;

    public $date_start;

    public $just_now;

    public $upload_file;

    public $phone1;

    public $phone2;

    public $phone3;

    public $viber_message_id;

    public function rules()
    {
        return [
            [['phone1', 'phone2', 'phone3'], 'integer'],
            [
                ['phone1', 'phone2', 'phone3'],
                function ($attribute, $params) {
                    if (strlen(''.$this->$attribute) < 11) {
                        $this->addError($attribute, 'Неправильная длина номера');
                    }
                },
            ],
            [['phone1', 'title'], 'required'],
            [['title'], 'string', 'max' => 50, 'min'=>3],
            [['text'], 'string', 'max' => 1000],
            [['image', 'url_button'], 'string', 'max' => 255],
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, png, gif',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
            ],
            [['title_button', 'alpha_name'], 'string', 'max' => 32],
            ['type', 'in', 'range' => array_keys(ViberMessage::listTypes())],
            [['time_start'], 'time', 'format' => 'php:H:i'],
            [['date_start'], 'date', 'format' => 'php:Y-m-d'],
            ['just_now', 'boolean'],

            [['date_start'], 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '>=', 'type' => 'date'],

        ];
    }

    public function attributeLabels()
    {
        return [

            'title' => 'Название рассылки',
            'text' => 'Поле для ввода техта SMS',
            'image' => 'Изображение',
            'title_button' => 'Текст на кнопке',
            'url_button' => 'Url по кнопке',
            'type' => 'Тип сообщения',
            'alpha_name' => 'Отправитель',
            'time_start' => 'Время отправки',
            'upload_file' => 'Картинка',
            'just_now' => 'Прямо сейчас',

        ];
    }

    public function uploadFile()
    {
        // get the uploaded file instance
        $image = UploadedFile::getInstance($this, 'upload_file');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }

        // generate random name for the file
        $this->image = time().'.'.$image->extension;

        // the uploaded image instance
        return $image;
    }

    /**
     * @return string
     */
    public function getUploadedFile()
    {
        // return a default image placeholder if your source avatar is not found
        $pic = isset($this->image) ? $this->image : 'default.png';

        return Yii::$app->params['fileUploadUrl'].$pic;
    }

    public function send()
    {
        $phones = [];
        if ($this->phone1) {
            $phones[] = $this->phone1;
        }
        if ($this->phone2) {
            $phones[] = $this->phone2;
        }
        if ($this->phone3) {
            $phones[] = $this->phone3;
        }
        if (!\Yii::$app->user->identity->checkBalance('viber', count($phones) )) {
            $this->addError('phone1', 'Недостаточно средств, для отправки сообщения. Для пополнения тестового баланса, пожалуйста обратитесь в службу поддержки через форму чата. Кнопка для доступа к чату находится справа, внизу');
            return false;
        }
        $cc = new ContactCollection([
                                        'user_id' => \Yii::$app->user->id,
                                        'title' => 'База для рассылки <'. $this->title . '> - ' . time(),
                                        'type' => 'viber',
                                        'size'=> count($phones),
                                        'created_at' => time(),
                                    ]);
        $vm = new ViberMessage([
                                   'user_id' => \Yii::$app->user->id,
                                   'title' => $this->title,
                                   'type' => $this->type,
                                   'text' => $this->text,
                                   'image' => $this->image,
                                   'title_button' => $this->title_button,
                                   'url_button' => $this->url_button,
                                   'status' => ViberMessage::STATUS_NEW,
                                   'cost' => count($phones),
                                   'provider' => 'smsonline'

                               ]);
        if ($this->just_now) {
            $vm->date_start = time();
            $vm->time_start = '00:00';
        } else {
            $vm->date_start = $this->date_start;
            $vm->time_start = $this->time_start;
        }

        $vm->date_finish = time() + 3600 * 24 * 1000;
        $vm->time_finish = '23:59';

        if ($this->type === ViberMessage::ONLYIMAGE || $this->type === ViberMessage::TEXTBUTTONIMAGE) {
            $upload_file = $this->uploadFile();
            if ($upload_file !== false) {
                $path = $this->getUploadedFile();
                $upload_file->saveAs($path);
            }
        }

        $vm->image = $this->image;
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($cc->save()) {
                $phone = new Phone();
                if ($phone->importText($cc->id, implode(',', $phones)) == 'ok') {


                    //списание баланса
                    //Yii::$app->user->identity->balance -= count($phones);
                    //Yii::$app->user->identity->save();

                    if ($vm->save()) {

                        $this->viber_message_id = $vm->id;
                        $vm_cc = new MessageContactCollection([
                                                                  'contact_collection_id' => $cc->id,
                                                                  'viber_message_id' => $vm->id,
                                                                  'title' => $this->title.time(),
                                                              ]);
                        if ($vm_cc->save()) {
                            $transaction->commit();
                        } else {
                            throw new Exception('VMCC: '.print_r($vm_cc->getErrors(), 1));
                        }
                    } else {
                        throw new Exception('VM: '.print_r($vm->getErrors(), 1));
                    }
                } else {

                    throw new Exception('Phone:'.print_r($phone->getErrors(), 1));
                }
            } else {

                throw new Exception('Contacts:'.print_r($cc->getErrors(), 1));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('title', $e->getMessage());

            return false;
        }



        if ($this->just_now) {
            $vm->alpha_name ='TEST';
            $v = new Viber($vm, $phones);
            if ($v->prepareTransaction()) {
                $v->sendMessage();
                $vm->setWait();
            } else {
                return false;
            }
        }

        return true;
    }
}